<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    evaluationsCandidates,
    evaluationsIndex,
    index,
} from '@/routes/recruiter';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Sparkles } from 'lucide-vue-next';

interface SkillBreakdown {
    technical: number;
    soft: number;
    domain: number;
}

interface Assessment {
    id: number;
    overall_match: number;
    summary: string;
    strengths: string[];
    gaps: string[];
    skill_breakdown: SkillBreakdown;
    evaluated_at: string;
}

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
}

interface Resume {
    id: number;
    resume_text: string;
}

const props = defineProps<{
    jobPosting: JobPosting;
    assessment: Assessment;
    resume: Resume;
}>();

const getMatchLabel = (score: number) => {
    if (score >= 80) return 'Strong Match';
    if (score >= 60) return 'Good Match';
    return 'Needs Review';
};

const getMatchLabelColor = (score: number) => {
    if (score >= 80) return 'text-green-400';
    if (score >= 60) return 'text-blue-400';
    return 'text-orange-400';
};

const skillCategories = [
    {
        name: 'Technical Skills',
        percentage: props.assessment.skill_breakdown.technical,
        color: 'bg-green-500',
    },
    {
        name: 'Soft Skills',
        percentage: props.assessment.skill_breakdown.soft,
        color: 'bg-blue-500',
    },
    {
        name: 'Domain Knowledge',
        percentage: props.assessment.skill_breakdown.domain,
        color: 'bg-yellow-500',
    },
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Recruiter',
        href: index().url,
    },
    {
        title: 'My Evaluations',
        href: evaluationsIndex().url,
    },
    {
        title: props.jobPosting.job_title,
        href: evaluationsCandidates({ jobPosting: props.jobPosting.id }).url,
    },
    {
        title: 'Assessment',
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Assessment - ${jobPosting.job_title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <Link
                                :href="
                                    evaluationsCandidates({
                                        jobPosting: jobPosting.id,
                                    }).url
                                "
                            >
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-white hover:bg-white/20"
                                >
                                    <ArrowLeft class="h-4 w-4" />
                                </Button>
                            </Link>
                            <div>
                                <h1 class="mb-1 text-3xl font-bold text-white">
                                    Candidate Assessment
                                </h1>
                                <p class="text-sm text-white/80">
                                    {{ jobPosting.job_title }} at
                                    {{ jobPosting.company }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-white/60">
                        Evaluated on {{ formatDate(assessment.evaluated_at) }}
                    </p>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Match Score Card -->
                <div
                    class="mb-8 border-2 border-[#e900ff] bg-[#2a2d3e] p-8 text-center"
                >
                    <h2
                        class="mb-4 text-sm font-bold uppercase tracking-wider text-[#e900ff]"
                    >
                        Candidate Match Score
                    </h2>
                    <div class="mb-4 text-7xl font-bold text-white">
                        {{ assessment.overall_match }}%
                    </div>
                    <div class="mb-4 flex items-center justify-center gap-2">
                        <span
                            :class="getMatchLabelColor(assessment.overall_match)"
                            class="text-lg font-semibold"
                        >
                            {{ getMatchLabel(assessment.overall_match) }}
                        </span>
                        <Sparkles
                            :class="getMatchLabelColor(assessment.overall_match)"
                            class="h-5 w-5"
                        />
                    </div>
                    <p class="text-sm leading-relaxed text-gray-300">
                        {{ assessment.summary }}
                    </p>
                </div>

                <!-- Detailed Breakdown -->
                <div class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h3 class="mb-6 text-xl font-bold text-white">
                        Detailed Breakdown
                    </h3>
                    <div class="space-y-6">
                        <div
                            v-for="skill in skillCategories"
                            :key="skill.name"
                            class="space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-300">{{
                                    skill.name
                                }}</span>
                                <span class="text-sm font-bold text-white"
                                    >{{ skill.percentage }}%</span
                                >
                            </div>
                            <div class="h-3 w-full overflow-hidden bg-gray-700">
                                <div
                                    :class="skill.color"
                                    :style="{
                                        width: `${skill.percentage}%`,
                                    }"
                                    class="h-full transition-all duration-500"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidate Strengths -->
                <div class="mb-8 border-2 border-green-500 bg-[#2a2d3e] p-8">
                    <h3 class="mb-6 text-2xl font-bold uppercase text-white">
                        Candidate Strengths
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="(strength, index) in assessment.strengths"
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <span class="text-2xl">✅</span>
                            <p
                                class="flex-1 text-base leading-relaxed text-gray-300"
                            >
                                {{ strength }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Gaps & Concerns -->
                <div
                    v-if="assessment.gaps.length > 0"
                    class="mb-8 border-2 border-orange-500 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold uppercase text-white">
                        Gaps & Areas for Discussion
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="(gap, index) in assessment.gaps"
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <span class="text-2xl">❌</span>
                            <p
                                class="flex-1 text-base leading-relaxed text-gray-300"
                            >
                                {{ gap }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resume Full Text -->
                <div class="border-2 border-gray-700 bg-[#2a2d3e] p-8">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Candidate Resume
                    </h3>
                    <div
                        class="max-h-96 overflow-y-auto border-l-4 border-gray-600 bg-gray-800 px-4 py-3"
                    >
                        <pre
                            class="whitespace-pre-wrap font-mono text-xs leading-relaxed text-gray-300"
                            >{{ resume.resume_text }}</pre
                        >
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
