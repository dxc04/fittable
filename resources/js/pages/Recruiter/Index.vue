<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { match } from '@/routes/recruiter';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowRight, Upload } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);

const form = useForm({
    jobDescription: '',
    candidateResume: '',
    resumeFile: null as File | null,
});

const resumeFile = ref<File | null>(null);

const handleClear = () => {
    form.reset();
    resumeFile.value = null;
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        resumeFile.value = target.files[0];
        form.resumeFile = target.files[0];
    }
};

const handleSubmit = () => {
    // Only send the data that's actually filled in
    const data: any = {
        jobDescription: form.jobDescription,
    };

    // Add either resume text or file, not both
    if (form.candidateResume) {
        data.candidateResume = form.candidateResume;
    } else if (form.resumeFile) {
        data.resumeFile = form.resumeFile;
    }

    form.transform(() => data).post(match.url(), {
        forceFormData: true,
        onSuccess: () => {
            // Keep the form filled for multiple evaluations
        },
        onError: (errors) => {
            console.error('Match calculation errors:', errors);
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Recruiter',
        href: '#',
    },
];
</script>

<template>
    <Head title="Job-Skills Matcher - Recruiter" />

    <AppLayout v-if="isAuthenticated" :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-12">
                <div class="container mx-auto text-center">
                    <h1 class="mb-3 text-4xl font-bold text-white">
                        Job-Skills Matcher
                    </h1>
                    <p class="text-lg text-white/80">
                        Paste the job description and candidate resume to see
                        the magic happen.
                    </p>
                </div>
            </div>

            <div class="container mx-auto px-6 py-12">
                <form @submit.prevent="handleSubmit">
                    <!-- Two Column Layout -->
                    <div class="mb-8 grid gap-6 lg:grid-cols-2">
                        <!-- Job Requirements -->
                        <div class="space-y-3">
                            <Label
                                for="jobDescription"
                                class="text-lg font-semibold text-white"
                            >
                                Job Requirements
                            </Label>
                            <Textarea
                                id="jobDescription"
                                v-model="form.jobDescription"
                                placeholder="Paste the full job description here..."
                                rows="25"
                                class="min-h-[524px] border-2 border-[#e900ff]/30 bg-[#2a2d3e] font-mono text-sm text-white placeholder:text-gray-400 focus:border-[#e900ff]"
                            />
                            <InputError :message="form.errors.jobDescription" />
                        </div>

                        <!-- Candidate Resume -->
                        <div class="space-y-3">
                            <Label
                                for="candidateResume"
                                class="text-lg font-semibold text-white"
                            >
                                Candidate Resume
                            </Label>

                            <!-- Upload File Section -->
                            <div class="space-y-2">
                                <div
                                    class="flex items-center justify-center border-2 border-dashed border-[#e900ff]/30 bg-[#2a2d3e] p-4 transition-colors hover:border-[#e900ff]/50"
                                >
                                    <div class="text-center">
                                        <Upload
                                            class="mx-auto h-8 w-8 text-gray-400"
                                        />
                                        <div class="mt-2">
                                            <label
                                                for="resumeFile"
                                                class="cursor-pointer text-sm font-medium text-[#e900ff] hover:text-[#d100e6]"
                                            >
                                                Click to upload resume
                                                <Input
                                                    id="resumeFile"
                                                    type="file"
                                                    accept=".pdf,.doc,.docx,.txt"
                                                    class="hidden"
                                                    @change="handleFileChange"
                                                />
                                            </label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400">
                                            PDF, DOC, DOCX, or TXT (max 5MB)
                                        </p>
                                        <p
                                            v-if="resumeFile"
                                            class="mt-2 bg-[#e900ff]/20 px-3 py-1 text-sm font-medium text-[#e900ff]"
                                        >
                                            Selected: {{ resumeFile.name }}
                                        </p>
                                    </div>
                                </div>
                                <InputError :message="form.errors.resumeFile" />
                            </div>

                            <!-- Divider -->
                            <div class="relative py-2">
                                <div class="absolute inset-0 flex items-center">
                                    <div
                                        class="w-full border-t border-gray-600"
                                    ></div>
                                </div>
                                <div
                                    class="relative flex justify-center text-xs"
                                >
                                    <span
                                        class="bg-[#1a1d2e] px-2 text-gray-400"
                                        >OR</span
                                    >
                                </div>
                            </div>

                            <!-- Paste Text Section -->
                            <Textarea
                                id="candidateResume"
                                v-model="form.candidateResume"
                                placeholder="Paste the candidate's resume here..."
                                rows="15"
                                class="min-h-[350px] border-2 border-[#e900ff]/30 bg-[#2a2d3e] font-mono text-sm text-white placeholder:text-gray-400 focus:border-[#e900ff]"
                            />
                            <InputError
                                :message="form.errors.candidateResume"
                            />
                        </div>
                    </div>

                    <!-- Tip -->
                    <div class="mb-8 text-center">
                        <p class="text-sm text-gray-400">
                            <span class="font-semibold">Tip:</span> Keep job
                            posting to evaluate multiple candidates efficiently.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            size="lg"
                            class="min-w-[140px] border-gray-600 bg-gray-700 text-white hover:bg-gray-600"
                            @click="handleClear"
                        >
                            Clear
                        </Button>
                        <Button
                            type="submit"
                            size="lg"
                            class="min-w-[200px] gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                            :disabled="
                                form.processing ||
                                !form.jobDescription ||
                                (!form.candidateResume && !form.resumeFile)
                            "
                        >
                            {{
                                form.processing
                                    ? 'Calculating...'
                                    : 'Calculate Match Score'
                            }}
                            <ArrowRight
                                v-if="!form.processing"
                                class="h-5 w-5"
                            />
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>

    <div v-else class="min-h-screen bg-[#1a1d2e]">
        <!-- Header -->
        <div class="bg-[#1e293b] px-6 py-12">
            <div class="container mx-auto text-center">
                <h1 class="mb-3 text-4xl font-bold text-white">
                    Job-Skills Matcher
                </h1>
                <p class="text-lg text-white/80">
                    Paste the job description and candidate resume to see the
                    magic happen.
                </p>
            </div>
        </div>

        <div class="container mx-auto px-6 py-12">
            <form @submit.prevent="handleSubmit">
                <!-- Two Column Layout -->
                <div class="mb-8 grid gap-6 lg:grid-cols-2">
                    <!-- Job Requirements -->
                    <div class="space-y-3">
                        <Label
                            for="jobDescription"
                            class="text-lg font-semibold text-white"
                        >
                            Job Requirements
                        </Label>
                        <Textarea
                            id="jobDescription"
                            v-model="form.jobDescription"
                            placeholder="Paste the full job description here..."
                            rows="20"
                            class="min-h-[500px] border-2 border-[#e900ff]/30 bg-[#2a2d3e] font-mono text-sm text-white placeholder:text-gray-400 focus:border-[#e900ff]"
                        />
                        <InputError :message="form.errors.jobDescription" />
                    </div>

                    <!-- Candidate Resume -->
                    <div class="space-y-3">
                        <Label
                            for="candidateResume"
                            class="text-lg font-semibold text-white"
                        >
                            Candidate Resume
                        </Label>

                        <!-- Upload File Section -->
                        <div class="space-y-2">
                            <div
                                class="flex items-center justify-center border-2 border-dashed border-[#e900ff]/30 bg-[#2a2d3e] p-4 transition-colors hover:border-[#e900ff]/50"
                            >
                                <div class="text-center">
                                    <Upload
                                        class="mx-auto h-8 w-8 text-gray-400"
                                    />
                                    <div class="mt-2">
                                        <label
                                            for="resumeFile"
                                            class="cursor-pointer text-sm font-medium text-[#e900ff] hover:text-[#d100e6]"
                                        >
                                            Click to upload resume
                                            <Input
                                                id="resumeFile"
                                                type="file"
                                                accept=".pdf,.doc,.docx,.txt"
                                                class="hidden"
                                                @change="handleFileChange"
                                            />
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">
                                        PDF, DOC, DOCX, or TXT (max 5MB)
                                    </p>
                                    <p
                                        v-if="resumeFile"
                                        class="mt-2 bg-[#e900ff]/20 px-3 py-1 text-sm font-medium text-[#e900ff]"
                                    >
                                        Selected: {{ resumeFile.name }}
                                    </p>
                                </div>
                            </div>
                            <InputError :message="form.errors.resumeFile" />
                        </div>

                        <!-- Divider -->
                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div
                                    class="w-full border-t border-gray-600"
                                ></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="bg-[#1a1d2e] px-2 text-gray-400"
                                    >OR</span
                                >
                            </div>
                        </div>

                        <!-- Paste Text Section -->
                        <Textarea
                            id="candidateResume"
                            v-model="form.candidateResume"
                            placeholder="Paste the candidate's resume here..."
                            rows="15"
                            class="min-h-[350px] border-2 border-[#e900ff]/30 bg-[#2a2d3e] font-mono text-sm text-white placeholder:text-gray-400 focus:border-[#e900ff]"
                        />
                        <InputError :message="form.errors.candidateResume" />
                    </div>
                </div>

                <!-- Tip -->
                <div class="mb-8 text-center">
                    <p class="text-sm text-gray-400">
                        <span class="font-semibold">Tip:</span> Keep job posting
                        to evaluate multiple candidates efficiently.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        size="lg"
                        class="min-w-[140px] border-gray-600 bg-gray-700 text-white hover:bg-gray-600"
                        @click="handleClear"
                    >
                        Clear
                    </Button>
                    <Button
                        type="submit"
                        size="lg"
                        class="min-w-[200px] gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        :disabled="
                            form.processing ||
                            !form.jobDescription ||
                            (!form.candidateResume && !form.resumeFile)
                        "
                    >
                        {{
                            form.processing
                                ? 'Calculating...'
                                : 'Calculate Match Score'
                        }}
                        <ArrowRight v-if="!form.processing" class="h-5 w-5" />
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
