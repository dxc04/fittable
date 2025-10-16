<script setup lang="ts">
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
import { show as showAssessment } from '@/routes/assessments';
import { assessResume } from '@/routes/job';
import { index } from '@/routes/job-postings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Briefcase,
    CheckCircle,
    Clock,
    DollarSign,
    Eye,
    FileCheck2,
    FileText,
    MapPin,
    Plus,
    Upload,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

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

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
}

interface UserResume {
    id: number;
    resume_text: string;
}

interface ExistingAssessment {
    id: number;
    overall_match: number;
}

const props = defineProps<{
    analysis: Analysis | null;
    originalText: string;
    jobPosting: JobPosting;
    userResume?: UserResume | null;
    existingAssessment?: ExistingAssessment | null;
}>();

const page = usePage();
const userRoles = computed(() => page.props.auth.user?.roles || []);
const isJobSeeker = computed(() => userRoles.value.includes('job_seeker'));

const isResumeModalOpen = ref(false);
const resumeFile = ref<File | null>(null);
const useExistingResume = ref(false);

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

const assessmentForm = useForm({
    resumeText: '',
    resumeFile: null as File | null,
    jobAdText: props.originalText,
    jobTitle: props.analysis?.jobTitle || props.jobPosting.job_title,
    company: props.analysis?.company || props.jobPosting.company,
    jobPostingId: props.jobPosting.id,
});

const handleNewAssessment = () => {
    assessmentForm.resumeText = props.userResume?.resume_text || '';
    assessmentForm.post(assessResume.url());
};

const handleAssessResumeClick = () => {
    isResumeModalOpen.value = true;
    useExistingResume.value = false;
    assessmentForm.reset();
    resumeFile.value = null;
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        resumeFile.value = target.files[0];
        assessmentForm.resumeFile = target.files[0];
        useExistingResume.value = false;
    }
};

const handleUseExistingResume = () => {
    useExistingResume.value = true;
    assessmentForm.resumeText = '';
    assessmentForm.resumeFile = null;
    resumeFile.value = null;
};

const submitResume = () => {
    const data: any = {
        jobAdText: assessmentForm.jobAdText,
        jobTitle: assessmentForm.jobTitle,
        company: assessmentForm.company,
        jobPostingId: assessmentForm.jobPostingId,
    };

    if (useExistingResume.value && props.userResume) {
        data.resumeText = props.userResume.resume_text;
    } else if (assessmentForm.resumeText) {
        data.resumeText = assessmentForm.resumeText;
    } else if (assessmentForm.resumeFile) {
        data.resumeFile = assessmentForm.resumeFile;
    }

    assessmentForm
        .transform(() => data)
        .post(assessResume.url(), {
            forceFormData: true,
            onSuccess: () => {
                isResumeModalOpen.value = false;
                assessmentForm.reset();
                resumeFile.value = null;
                useExistingResume.value = false;
            },
            onError: (errors) => {
                console.error('Resume submission errors:', errors);
            },
        });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Postings',
        href: index().url,
    },
    {
        title: props.jobPosting.job_title,
        href: '#',
    },
];
</script>

<template>
    <Head :title="`${jobPosting.job_title} - Analysis`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="mb-6 flex items-center justify-between">
                        <h1 class="text-4xl font-bold text-white">
                            {{ jobPosting.job_title }}
                        </h1>
                        <div class="flex items-center gap-2">
                            <Link :href="index().url">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-white hover:bg-white/20"
                                >
                                    <ArrowLeft class="mr-2 h-4 w-4" />
                                    Back to List
                                </Button>
                            </Link>
                            <div
                                v-if="isJobSeeker && userResume"
                                class="flex gap-2"
                            >
                                <Button
                                    @click="handleNewAssessment"
                                    class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                                    :disabled="assessmentForm.processing"
                                >
                                    <Plus class="h-4 w-4" />
                                    New Assessment
                                </Button>
                                <Button
                                    @click="handleAssessResumeClick"
                                    class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                                    :disabled="assessmentForm.processing"
                                >
                                    <Plus class="h-4 w-4" />
                                    Assess Resume for this Job
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="analysis"
                        class="flex flex-wrap items-center gap-6 text-white/90"
                    >
                        <div
                            v-if="analysis.jobType"
                            class="flex items-center gap-2"
                        >
                            <Briefcase class="h-5 w-5" />
                            <span>{{ analysis.jobType }}</span>
                        </div>
                        <div
                            v-if="analysis.location"
                            class="flex items-center gap-2"
                        >
                            <MapPin class="h-5 w-5" />
                            <span>{{ analysis.location }}</span>
                        </div>
                        <div
                            v-if="analysis.salaryRange"
                            class="flex items-center gap-2"
                        >
                            <DollarSign class="h-5 w-5" />
                            <span>{{ analysis.salaryRange }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="!analysis"
                class="container mx-auto px-6 py-8 text-center"
            >
                <p class="text-gray-400">
                    No analysis available for this job posting.
                </p>
            </div>

            <div v-else class="container mx-auto px-6 py-8">
                <!-- Existing Assessment Alert -->
                <div
                    v-if="isJobSeeker && existingAssessment"
                    class="mb-8 border-2 border-[#e900ff] bg-[#e900ff]/10 p-6"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <FileCheck2 class="h-8 w-8 text-[#e900ff]" />
                            <div>
                                <h3 class="font-bold text-white">
                                    You have an existing assessment for this
                                    job!
                                </h3>
                                <p class="text-sm text-gray-300">
                                    Match Score:
                                    {{ existingAssessment.overall_match }}%
                                </p>
                            </div>
                        </div>
                        <Button
                            variant="outline"
                            class="gap-2 border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/20"
                            as-child
                        >
                            <Link
                                :href="
                                    showAssessment(existingAssessment.id).url
                                "
                            >
                                <Eye class="h-4 w-4" />
                                View Assessment
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Company Background -->
                <div
                    v-if="analysis.companyBackground"
                    class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h2 class="mb-4 text-2xl font-bold text-white">
                        About the Company
                    </h2>
                    <p class="leading-relaxed text-gray-300">
                        {{ analysis.companyBackground }}
                    </p>
                </div>

                <!-- Summary Section -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h2 class="mb-4 text-2xl font-bold text-white">About the Role</h2>
                    <p class="leading-relaxed text-gray-300">
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


                <!-- Responsibilities -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h2 class="mb-4 text-2xl font-bold text-white">
                        Key Responsibilities
                    </h2>
                    <div class="space-y-4">
                        <div
                            v-for="(resp, index) in analysis.responsibilities"
                            :key="index"
                            class="border-l-4 bg-[#1a1d2e] p-4"
                            :class="{
                                'border-red-500':
                                    resp.importance && resp.importance >= 80,
                                'border-orange-500':
                                    resp.importance &&
                                    resp.importance >= 50 &&
                                    resp.importance < 80,
                                'border-blue-500':
                                    resp.importance && resp.importance < 50,
                                'border-gray-500': !resp.importance,
                            }"
                        >
                            <div class="mb-2 flex items-center gap-2">
                                <CheckCircle
                                    class="h-5 w-5"
                                    :class="{
                                        'text-red-500':
                                            resp.importance &&
                                            resp.importance >= 80,
                                        'text-orange-500':
                                            resp.importance &&
                                            resp.importance >= 50 &&
                                            resp.importance < 80,
                                        'text-blue-500':
                                            resp.importance &&
                                            resp.importance < 50,
                                        'text-gray-500': !resp.importance,
                                    }"
                                />
                                <h3 class="font-bold text-white">
                                    {{ resp.title }}
                                </h3>
                                <Badge
                                    v-if="resp.importance"
                                    variant="outline"
                                    class="ml-auto text-xs"
                                    :class="{
                                        'border-red-500 bg-red-500/10 text-red-400':
                                            resp.importance >= 80,
                                        'border-orange-500 bg-orange-500/10 text-orange-400':
                                            resp.importance >= 50 &&
                                            resp.importance < 80,
                                        'border-blue-500 bg-blue-500/10 text-blue-400':
                                            resp.importance < 50,
                                    }"
                                >
                                    {{
                                        resp.importance >= 80
                                            ? 'Critical'
                                            : resp.importance >= 50
                                              ? 'Important'
                                              : 'Standard'
                                    }}
                                </Badge>
                            </div>
                            <p class="text-sm text-gray-300">
                                {{ resp.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Benefits -->
                <div
                    v-if="analysis.benefits.length > 0"
                    class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h2 class="mb-4 text-2xl font-bold text-white">Benefits</h2>
                    <ul class="space-y-2">
                        <li
                            v-for="(benefit, index) in analysis.benefits"
                            :key="index"
                            class="flex items-start gap-2 text-gray-300"
                        >
                            <CheckCircle
                                class="mt-1 h-4 w-4 flex-shrink-0 text-green-400"
                            />
                            <span>{{ benefit }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Hiring Process -->
                <div
                    v-if="analysis.hiringProcess"
                    class="border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h2
                        class="mb-4 flex items-center gap-2 text-2xl font-bold text-white"
                    >
                        <Clock class="h-6 w-6 text-[#e900ff]" />
                        Hiring Process
                    </h2>
                    <p class="leading-relaxed text-gray-300">
                        {{ analysis.hiringProcess }}
                    </p>
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
                        Submit Your Resume
                    </DialogTitle>
                    <DialogDescription class="text-gray-400">
                        Choose how you want to submit your resume for this job
                        assessment.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-6 py-4">
                    <!-- Use Existing Resume Option -->
                    <div
                        v-if="userResume"
                        class="rounded border-2 border-gray-700 bg-[#2a2d3e] p-4"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <FileText class="h-8 w-8 text-[#e900ff]" />
                                <div>
                                    <h4 class="font-semibold text-white">
                                        Use Latest Resume
                                    </h4>
                                    <p class="text-sm text-gray-400">
                                        Use your most recently uploaded resume
                                    </p>
                                </div>
                            </div>
                            <Button
                                @click="handleUseExistingResume"
                                :variant="
                                    useExistingResume ? 'default' : 'outline'
                                "
                                class="gap-2"
                                :class="
                                    useExistingResume
                                        ? 'bg-[#e900ff] text-white hover:bg-[#d100e6]'
                                        : 'border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10'
                                "
                            >
                                {{ useExistingResume ? 'Selected' : 'Select' }}
                            </Button>
                        </div>
                    </div>

                    <div class="text-center text-sm text-gray-500">
                        {{ userResume ? 'OR' : '' }}
                    </div>

                    <!-- Paste Resume Text -->
                    <div class="space-y-2">
                        <Label class="text-white">Paste Resume Text</Label>
                        <Textarea
                            v-model="assessmentForm.resumeText"
                            placeholder="Paste your resume text here..."
                            rows="8"
                            class="border-gray-700 bg-[#2a2d3e] text-white placeholder:text-gray-500"
                            @input="useExistingResume = false"
                        />
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
                            assessmentForm.processing ||
                            (!useExistingResume &&
                                !assessmentForm.resumeText &&
                                !assessmentForm.resumeFile)
                        "
                        class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                    >
                        {{
                            assessmentForm.processing
                                ? 'Submitting...'
                                : 'Submit Resume'
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
