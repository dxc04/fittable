<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { assessResume, index } from '@/routes/job';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Briefcase,
    CheckCircle,
    Clock,
    DollarSign,
    FileText,
    MapPin,
    Upload,
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Responsibility {
    title: string;
    importance: number;
    description: string;
}

interface Requirement {
    title: string;
    type: string;
    priority: number;
}

interface Analysis {
    jobTitle: string;
    company: string;
    companyBackground: string;
    location: string;
    jobType: string;
    summary: string;
    requiredSkills: string[];
    niceToHaveSkills: string[];
    responsibilities: Responsibility[];
    requirements: Requirement[];
    benefits: string[];
    salaryRange: string;
    hiringProcess: string;
}

const props = defineProps<{
    analysis: Analysis;
    originalText: string;
}>();

const isResumeModalOpen = ref(false);
const resumeFile = ref<File | null>(null);

const resumeForm = useForm({
    resumeText: '',
    resumeFile: null as File | null,
    jobAdText: props.originalText,
    jobTitle: props.analysis.jobTitle,
    company: props.analysis.company,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        resumeFile.value = target.files[0];
        resumeForm.resumeFile = target.files[0];
    }
};

const submitResume = () => {
    // Only send the data that's actually filled in
    const data: any = {
        jobAdText: resumeForm.jobAdText,
        jobTitle: resumeForm.jobTitle,
        company: resumeForm.company,
    };

    // Add either resume text or file, not both
    if (resumeForm.resumeText) {
        data.resumeText = resumeForm.resumeText;
    } else if (resumeForm.resumeFile) {
        data.resumeFile = resumeForm.resumeFile;
    }

    resumeForm
        .transform(() => data)
        .post(assessResume.url(), {
            forceFormData: true,
            onSuccess: () => {
                isResumeModalOpen.value = false;
                resumeForm.reset();
                resumeFile.value = null;
            },
            onError: (errors) => {
                console.error('Resume submission errors:', errors);
            },
        });
};

const getPriorityLabel = (importance: number) => {
    if (importance >= 80) return 'High Priority';
    if (importance >= 50) return 'Medium Priority';
    return 'Low Priority';
};

const getPriorityColor = (importance: number) => {
    if (importance >= 80) return 'bg-red-50 text-red-700 border-red-200';
    if (importance >= 50)
        return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    return 'bg-green-50 text-green-700 border-green-200';
};

const getCheckColor = (importance: number) => {
    if (importance >= 80) return 'text-red-600';
    if (importance >= 50) return 'text-yellow-600';
    return 'text-green-600';
};

const getRequirementColor = (index: number) => {
    const colors = [
        'bg-blue-500',
        'bg-purple-500',
        'bg-cyan-500',
        'bg-emerald-500',
        'bg-orange-500',
    ];
    return colors[index % colors.length];
};

// Split requirements into main requirements and additional requirements
const mainRequirements = props.analysis.requirements.slice(0, 5);
const additionalRequirements = props.analysis.requirements.slice(5);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Analysis',
        href: index().url,
    },
    {
        title: props.analysis.jobTitle || 'Results',
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Analysis: ${analysis.jobTitle}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="mb-2 text-3xl font-bold text-white">
                                Job Analysis Results
                            </h1>
                            <p class="text-sm text-white/90">
                                {{ analysis.jobTitle }} at
                                {{ analysis.company }}
                            </p>
                        </div>
                        <Link :href="index.url()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-white hover:bg-white/20"
                            >
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                New Analysis
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Assess Resume CTA -->
                <div
                    class="mb-8 border-2 border-[#e900ff] bg-[#e900ff] p-6 text-center"
                >
                    <h2 class="mb-2 text-xl font-bold text-white">
                        Ready to Apply?
                    </h2>
                    <p class="mb-4 text-sm text-white/90">
                        Get an AI-powered assessment of how well your resume
                        matches this position
                    </p>
                    <Button
                        size="lg"
                        class="gap-2 bg-white text-[#e900ff] hover:bg-gray-100"
                        @click="isResumeModalOpen = true"
                    >
                        <FileText class="h-5 w-5" />
                        Assess Your Resume Against This Job
                    </Button>
                </div>

                <!-- Job Overview -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h2 class="mb-3 text-3xl font-bold text-white">
                        {{ analysis.jobTitle }}
                    </h2>
                    <div
                        class="flex flex-wrap items-center gap-4 text-sm text-gray-300"
                    >
                        <div class="flex items-center gap-1.5">
                            <Briefcase class="h-4 w-4" />
                            <span>{{ analysis.jobType }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <Clock class="h-4 w-4" />
                            <span>Remote / Hybrid</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <MapPin class="h-4 w-4" />
                            <span>{{ analysis.location }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <DollarSign class="h-4 w-4" />
                            <span>{{ analysis.salaryRange }}</span>
                        </div>
                    </div>
                </div>

                <!-- Company Background -->
                <div
                    v-if="
                        analysis.companyBackground &&
                        analysis.companyBackground !== 'Not specified'
                    "
                    class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-3 text-xl font-bold text-white">
                        About the Company
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-300">
                        {{ analysis.companyBackground }}
                    </p>
                </div>

                <!-- About the Role -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h3 class="mb-3 text-xl font-bold text-white">
                        About the Role
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-300">
                        {{ analysis.summary }}
                    </p>
                </div>

                <!-- Skills Section -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <!-- Required Skills -->
                    <div v-if="analysis.requiredSkills.length > 0" class="mb-8">
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Required Skills
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <Badge
                                v-for="skill in analysis.requiredSkills"
                                :key="skill"
                                class="border-0 bg-[#e900ff] px-4 py-2 text-sm font-medium text-white hover:bg-[#d100e6]"
                            >
                                {{ skill }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Nice-to-Have Skills -->
                    <div v-if="analysis.niceToHaveSkills.length > 0">
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Nice-to-Have Skills
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <Badge
                                v-for="skill in analysis.niceToHaveSkills"
                                :key="skill"
                                class="border-0 bg-[#a855f7] px-4 py-2 text-sm font-medium text-white hover:bg-[#9333ea]"
                            >
                                {{ skill }}
                            </Badge>
                        </div>
                    </div>

                    <p
                        v-if="
                            analysis.requiredSkills.length === 0 &&
                            analysis.niceToHaveSkills.length === 0
                        "
                        class="text-center text-sm text-gray-400"
                    >
                        No specific skills identified
                    </p>
                </div>

                <!-- Experience Requirements -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Experience Requirements
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="(req, index) in mainRequirements"
                            :key="index"
                        >
                            <div
                                class="mb-2 flex items-center justify-between text-sm"
                            >
                                <span class="font-semibold text-gray-300">{{
                                    req.title
                                }}</span>
                                <span class="text-xs text-gray-400">{{
                                    req.type === 'must-have'
                                        ? req.priority + '%'
                                        : req.priority / 10 + '+ years'
                                }}</span>
                            </div>
                            <div class="h-3 w-full overflow-hidden bg-gray-700">
                                <div
                                    :class="getRequirementColor(index)"
                                    class="h-full transition-all duration-500"
                                    :style="{ width: `${req.priority}%` }"
                                />
                            </div>
                        </div>

                        <!-- Additional Requirements Box -->
                        <div
                            v-if="additionalRequirements.length > 0"
                            class="mt-6 border-2 border-gray-600 bg-[#1a1d2e] p-4"
                        >
                            <h4 class="mb-3 font-semibold text-white">
                                Additional Requirements
                            </h4>
                            <ul class="space-y-2">
                                <li
                                    v-for="(
                                        req, index
                                    ) in additionalRequirements"
                                    :key="index"
                                    class="flex items-start gap-2 text-sm text-gray-300"
                                >
                                    <span
                                        class="mt-1.5 h-1 w-1 shrink-0 bg-gray-400"
                                    ></span>
                                    <span>{{ req.title }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Two Column Layout: Hiring Process & Benefits -->
                <div class="mb-8 grid gap-6 lg:grid-cols-2">
                    <!-- Hiring Process -->
                    <div
                        v-if="
                            analysis.hiringProcess &&
                            analysis.hiringProcess !== 'Not specified'
                        "
                        class="border-2 border-gray-700 bg-[#2a2d3e] p-8"
                    >
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Hiring Process
                        </h3>
                        <p class="text-sm leading-relaxed text-gray-300">
                            {{ analysis.hiringProcess }}
                        </p>
                    </div>

                    <!-- Benefits -->
                    <div
                        v-if="analysis.benefits.length > 0"
                        class="border-2 border-gray-700 bg-[#2a2d3e] p-8"
                    >
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Benefits & Perks
                        </h3>
                        <div class="space-y-2.5">
                            <div
                                v-for="(benefit, index) in analysis.benefits"
                                :key="index"
                                class="flex items-start gap-2 text-sm"
                            >
                                <CheckCircle
                                    class="mt-0.5 h-4 w-4 shrink-0 text-green-500"
                                />
                                <span class="text-gray-300">{{ benefit }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Responsibilities -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Key Responsibilities
                    </h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div
                            v-for="(
                                responsibility, index
                            ) in analysis.responsibilities"
                            :key="index"
                            class="flex flex-col gap-3 border-2 border-gray-600 bg-[#1a1d2e] p-5"
                        >
                            <div class="flex items-start gap-3">
                                <CheckCircle
                                    :class="
                                        getCheckColor(responsibility.importance)
                                    "
                                    class="mt-0.5 h-5 w-5 shrink-0"
                                />
                                <div class="flex-1">
                                    <h4
                                        class="mb-1 text-base font-semibold text-white"
                                    >
                                        {{ responsibility.title }}
                                    </h4>
                                    <p
                                        class="text-sm leading-relaxed text-gray-300"
                                    >
                                        {{ responsibility.description }}
                                    </p>
                                </div>
                            </div>
                            <Badge
                                :class="
                                    getPriorityColor(responsibility.importance)
                                "
                                class="w-fit border-2 px-3 py-1 text-xs font-medium"
                            >
                                {{
                                    getPriorityLabel(responsibility.importance)
                                }}
                            </Badge>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resume Assessment Modal -->
            <Dialog v-model:open="isResumeModalOpen">
                <DialogContent class="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Assess Your Resume</DialogTitle>
                        <DialogDescription>
                            Upload your resume or paste the text to see how well
                            you match with this job position.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="space-y-6">
                        <!-- Upload File Section -->
                        <div class="space-y-2">
                            <Label for="resumeFile">Upload Resume File</Label>
                            <div
                                class="flex items-center justify-center border-2 border-dashed border-gray-300 bg-gray-50 p-6 transition-colors hover:border-gray-400 hover:bg-gray-100"
                            >
                                <div class="text-center">
                                    <Upload
                                        class="mx-auto h-10 w-10 text-gray-400"
                                    />
                                    <div class="mt-3">
                                        <label
                                            for="resumeFile"
                                            class="cursor-pointer text-sm font-medium text-blue-600 hover:text-blue-500"
                                        >
                                            Click to upload
                                            <Input
                                                id="resumeFile"
                                                type="file"
                                                accept=".pdf,.doc,.docx,.txt"
                                                class="hidden"
                                                @change="handleFileChange"
                                            />
                                        </label>
                                        <span class="text-sm text-gray-500">
                                            or drag and drop</span
                                        >
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        PDF, DOC, DOCX, or TXT (max 5MB)
                                    </p>
                                    <p class="mt-1 text-xs text-amber-600">
                                        Note: PDFs must be text-based (not
                                        scanned images)
                                    </p>
                                    <p
                                        v-if="resumeFile"
                                        class="mt-3 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-900"
                                    >
                                        Selected: {{ resumeFile.name }}
                                    </p>
                                </div>
                            </div>
                            <InputError
                                :message="resumeForm.errors.resumeFile"
                            />
                        </div>

                        <!-- Divider -->
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div
                                    class="w-full border-t border-gray-300"
                                ></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-2 text-gray-500"
                                    >OR</span
                                >
                            </div>
                        </div>

                        <!-- Paste Text Section -->
                        <div class="space-y-2">
                            <Label for="resumeText">Paste Resume Text</Label>
                            <Textarea
                                id="resumeText"
                                v-model="resumeForm.resumeText"
                                placeholder="Paste your resume here..."
                                rows="10"
                                class="font-mono text-sm"
                            />
                            <InputError
                                :message="resumeForm.errors.resumeText"
                            />
                            <p class="text-xs text-muted-foreground">
                                Paste your complete resume content
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            variant="outline"
                            @click="isResumeModalOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            @click="submitResume"
                            :disabled="
                                resumeForm.processing ||
                                (!resumeForm.resumeText &&
                                    !resumeForm.resumeFile)
                            "
                        >
                            {{
                                resumeForm.processing
                                    ? 'Analyzing...'
                                    : 'Assess Resume'
                            }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
