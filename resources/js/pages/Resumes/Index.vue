<script setup lang="ts">
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
import { index, show, store } from '@/routes/resumes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Calendar, FileText, Plus, TrendingUp, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

interface Resume {
    id: number;
    resume_text: string;
    created_at: string;
    assessments_count: number;
}

defineProps<{
    resumes: Resume[];
}>();

const isResumeModalOpen = ref(false);
const resumeFile = ref<File | null>(null);

const resumeForm = useForm({
    resumeText: '',
    resumeFile: null as File | null,
});

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getExcerpt = (text: string, maxLength: number = 150) => {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const pluralizeAssessment = (count: number) => {
    return count === 1 ? 'Assessment' : 'Assessments';
};

const handleSaveResumeClick = () => {
    isResumeModalOpen.value = true;
    resumeForm.reset();
    resumeFile.value = null;
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        resumeFile.value = target.files[0];
        resumeForm.resumeFile = target.files[0];
    }
};

const submitResume = () => {
    const data: any = {};

    if (resumeForm.resumeText) {
        data.resumeText = resumeForm.resumeText;
    } else if (resumeForm.resumeFile) {
        data.resumeFile = resumeForm.resumeFile;
    }

    resumeForm
        .transform(() => data)
        .post(store.url(), {
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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resumes',
        href: index().url,
    },
];
</script>

<template>
    <Head title="Resumes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e] px-6 py-12">
            <div class="mx-auto max-w-6xl">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-white">
                            Resumes
                        </h1>
                        <p class="text-lg text-gray-400">
                            View all your uploaded resumes and their assessments
                        </p>
                    </div>
                    <Button
                        @click="handleSaveResumeClick"
                        class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                    >
                        <Plus class="h-4 w-4" />
                        Add New Resume
                    </Button>
                </div>

                <!-- Empty State -->
                <div
                    v-if="resumes.length === 0"
                    class="border-2 border-dashed border-gray-700 bg-[#2a2d3e] p-12 text-center"
                >
                    <FileText class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                    <h3 class="mb-2 text-xl font-bold text-white">
                        No resumes yet
                    </h3>
                    <p class="mb-6 text-gray-400">
                        Save your first resume to get started
                    </p>
                    <Button
                        @click="handleSaveResumeClick"
                        class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                    >
                        <Plus class="h-4 w-4" />
                        Save New Resume
                    </Button>
                </div>

                <!-- Resumes Grid -->
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="resume in resumes"
                        :key="resume.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] p-6 transition-all hover:border-[#e900ff]"
                    >
                        <!-- Icon -->
                        <div class="mb-4 flex items-start justify-between">
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center border-2 border-[#e900ff] bg-[#e900ff]/10"
                            >
                                <FileText class="h-6 w-6 text-[#e900ff]" />
                            </div>
                            <div
                                class="flex items-center gap-2 border border-gray-600 bg-[#1a1d2e] px-3 py-1 text-sm"
                            >
                                <TrendingUp class="h-4 w-4 text-[#e900ff]" />
                                <span class="font-bold text-white">
                                    {{ resume.assessments_count }}
                                </span>
                                <span class="text-gray-400">
                                    {{ pluralizeAssessment(resume.assessments_count) }}
                                </span>
                            </div>
                        </div>

                        <!-- Resume Preview -->
                        <div class="mb-4">
                            <h3 class="mb-2 text-lg font-bold text-white">
                                Resume #{{ resume.id }}
                            </h3>
                            <p class="line-clamp-4 text-sm text-gray-300">
                                {{ getExcerpt(resume.resume_text, 200) }}
                            </p>
                        </div>

                        <!-- Date -->
                        <div
                            class="mb-4 flex items-center gap-2 text-xs text-gray-500"
                        >
                            <Calendar class="h-3 w-3" />
                            {{ formatDate(resume.created_at) }}
                        </div>

                        <!-- View Button -->
                        <Button
                            variant="outline"
                            class="w-full border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10"
                            as-child
                        >
                            <Link :href="show(resume.id).url">
                                View Details
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resume Modal -->
        <Dialog
            :open="isResumeModalOpen"
            @update:open="isResumeModalOpen = $event"
        >
            <DialogContent
                class="border-2 border-[#e900ff] bg-[#1a1d2e] sm:max-w-[600px]"
            >
                <DialogHeader>
                    <DialogTitle class="text-white">
                        Save New Resume
                    </DialogTitle>
                    <DialogDescription class="text-gray-400">
                        Paste your resume text or upload a file to save it for
                        later use.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-6 py-4">
                    <!-- Paste Resume Text -->
                    <div class="space-y-2">
                        <Label class="text-white">Paste Resume Text</Label>
                        <Textarea
                            v-model="resumeForm.resumeText"
                            placeholder="Paste your resume text here..."
                            rows="10"
                            class="border-gray-700 bg-[#2a2d3e] text-white placeholder:text-gray-500"
                        />
                        <p
                            v-if="resumeForm.errors.resumeText"
                            class="text-sm text-red-400"
                        >
                            {{ resumeForm.errors.resumeText }}
                        </p>
                    </div>

                    <div class="text-center text-sm text-gray-500">OR</div>

                    <!-- Upload Resume File -->
                    <div class="space-y-2">
                        <Label class="text-white">Upload Resume File</Label>
                        <div class="flex items-center gap-2">
                            <Input
                                type="file"
                                accept=".pdf,.doc,.docx,.txt"
                                @change="handleFileChange"
                                class="border-gray-700 bg-[#2a2d3e] text-white file:text-white"
                            />
                            <Upload class="h-5 w-5 text-gray-400" />
                        </div>
                        <p v-if="resumeFile" class="text-sm text-[#e900ff]">
                            Selected: {{ resumeFile.name }}
                        </p>
                        <p
                            v-if="resumeForm.errors.resumeFile"
                            class="text-sm text-red-400"
                        >
                            {{ resumeForm.errors.resumeFile }}
                        </p>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="isResumeModalOpen = false"
                        class="border-gray-700 text-white hover:bg-white/10"
                    >
                        Cancel
                    </Button>
                    <Button
                        @click="submitResume"
                        :disabled="
                            resumeForm.processing ||
                            (!resumeForm.resumeText && !resumeForm.resumeFile)
                        "
                        class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                    >
                        {{
                            resumeForm.processing ? 'Saving...' : 'Save Resume'
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
