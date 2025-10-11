<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class DocumentTextExtractor
{
    /**
     * Extract text from an uploaded document file.
     */
    public function extractText(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractFromPdf($file),
            'docx' => $this->extractFromDocx($file),
            'doc' => $this->extractFromDoc($file),
            'txt' => $this->extractFromTxt($file),
            default => throw new \InvalidArgumentException("Unsupported file type: {$extension}"),
        };
    }

    /**
     * Extract text from a PDF file.
     */
    protected function extractFromPdf(UploadedFile $file): string
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();

            // Clean up extra whitespace
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            // Check if PDF is text-based (has extractable text)
            if (empty($text) || strlen($text) < 50) {
                throw new \RuntimeException(
                    'This PDF appears to be image-based or scanned. '.
                    'Please use a text-based PDF, convert your resume to DOCX format, or paste the text directly.'
                );
            }

            return $text;
        } catch (\RuntimeException $e) {
            // Re-throw our custom runtime exceptions
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'Failed to parse PDF file. Please ensure the PDF is text-based (not scanned/image-based) '.
                'or try uploading a DOCX file instead.'
            );
        }
    }

    /**
     * Extract text from a DOCX file.
     */
    protected function extractFromDocx(UploadedFile $file): string
    {
        try {
            $phpWord = IOFactory::load($file->getRealPath());
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText().' ';
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText().' ';
                            }
                        }
                    }
                }
            }

            $text = trim($text);

            if (empty($text)) {
                throw new \RuntimeException('Could not extract text from Word document.');
            }

            return $text;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to parse Word document: '.$e->getMessage());
        }
    }

    /**
     * Extract text from a DOC file.
     */
    protected function extractFromDoc(UploadedFile $file): string
    {
        // DOC files (older format) are more complex to parse
        // For now, we'll try to read it as DOCX, and if that fails, suggest conversion
        try {
            return $this->extractFromDocx($file);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to parse .doc file. Please convert to .docx or paste the text directly.');
        }
    }

    /**
     * Extract text from a TXT file.
     */
    protected function extractFromTxt(UploadedFile $file): string
    {
        $text = file_get_contents($file->getRealPath());

        if ($text === false) {
            throw new \RuntimeException('Failed to read text file.');
        }

        $text = trim($text);

        if (empty($text)) {
            throw new \RuntimeException('Text file is empty.');
        }

        return $text;
    }
}
