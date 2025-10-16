<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/job';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Sparkles } from 'lucide-vue-next';

interface SkillBreakdown {
    technical: number;
    soft: number;
    domain: number;
}

interface ApplicationStrategy {
    resumeOptimization: string[];
    coverLetterFocus: string[];
    howToAddressGaps: string[];
}

interface InterviewPreparation {
    likelyQuestions: string[];
    topicsToStudy: string[];
    storiesToPrepare: string[];
}

interface PersonalizedRecommendation {
    shouldApply: string;
    biggestAdvantage: string;
    nextStep: string;
}

interface Assessment {
    overallMatch: number;
    skillBreakdown: SkillBreakdown;
    summary: string;
    strengths: string[];
    gaps: string[];
    applicationStrategy?: ApplicationStrategy;
    interviewPreparation?: InterviewPreparation;
    personalizedRecommendation?: PersonalizedRecommendation;
}

interface JobInfo {
    jobTitle: string;
    company: string;
}

const props = defineProps<{
    assessment: Assessment;
    jobInfo: JobInfo;
}>();

const getMatchLabel = (score: number) => {
    if (score >= 80) return 'Strong Match';
    if (score >= 60) return 'Good Match';
    return 'Needs Improvement';
};

const getMatchLabelColor = (score: number) => {
    if (score >= 80) return 'text-green-400';
    if (score >= 60) return 'text-blue-400';
    return 'text-orange-400';
};

// Map skill breakdown from backend to display format
const skillCategories = [
    {
        name: 'Technical Skills',
        percentage: props.assessment.skillBreakdown.technical,
        color: 'bg-green-500',
    },
    {
        name: 'Soft Skills',
        percentage: props.assessment.skillBreakdown.soft,
        color: 'bg-blue-500',
    },
    {
        name: 'Domain Knowledge',
        percentage: props.assessment.skillBreakdown.domain,
        color: 'bg-yellow-500',
    },
];

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Analysis',
        href: index().url,
    },
    {
        title: props.jobInfo.jobTitle || 'Assessment',
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Resume Assessment - ${jobInfo.jobTitle}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="mb-2 text-3xl font-bold text-white">
                                Resume Assessment
                            </h1>
                            <p class="text-sm text-white/90">
                                {{ jobInfo.jobTitle }} at {{ jobInfo.company }}
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
                <!-- Match Score Card -->
                <div
                    class="mb-8 border-2 border-[#e900ff] bg-[#2a2d3e] p-8 text-center"
                >
                    <h2
                        class="mb-4 text-sm font-bold tracking-wider text-[#e900ff] uppercase"
                    >
                        Your Match Score
                    </h2>
                    <div class="mb-4 text-7xl font-bold text-white">
                        {{ assessment.overallMatch }}%
                    </div>
                    <div class="mb-4 flex items-center justify-center gap-2">
                        <span
                            :class="getMatchLabelColor(assessment.overallMatch)"
                            class="text-lg font-semibold"
                        >
                            {{ getMatchLabel(assessment.overallMatch) }}
                        </span>
                        <Sparkles
                            :class="getMatchLabelColor(assessment.overallMatch)"
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

                <!-- Your Strengths to Emphasize -->
                <div class="mb-8 border-2 border-green-500 bg-[#2a2d3e] p-8">
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Your Strengths to Emphasize
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

                <!-- Your Gaps to Address Proactively -->
                <div
                    v-if="assessment.gaps.length > 0"
                    class="mb-8 border-2 border-orange-500 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Your Gaps to Address Proactively
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

                <!-- Application Strategy -->
                <div
                    v-if="assessment.applicationStrategy"
                    class="mb-8 border-2 border-purple-500 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Application Strategy
                    </h3>

                    <!-- Resume Optimization -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-lg font-semibold text-purple-400">
                            Resume Optimization
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(tip, index) in assessment
                                    .applicationStrategy.resumeOptimization"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ tip }}
                            </li>
                        </ul>
                    </div>

                    <!-- Cover Letter Focus -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-lg font-semibold text-purple-400">
                            Cover Letter Focus
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(point, index) in assessment
                                    .applicationStrategy.coverLetterFocus"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ point }}
                            </li>
                        </ul>
                    </div>

                    <!-- How to Address Gaps -->
                    <div>
                        <h4 class="mb-3 text-lg font-semibold text-purple-400">
                            How to Address Gaps
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(strategy, index) in assessment
                                    .applicationStrategy.howToAddressGaps"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ strategy }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Interview Preparation -->
                <div
                    v-if="assessment.interviewPreparation"
                    class="mb-8 border-2 border-cyan-500 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Interview Preparation
                    </h3>

                    <!-- Likely Questions -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-lg font-semibold text-cyan-400">
                            Likely Questions
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(question, index) in assessment
                                    .interviewPreparation.likelyQuestions"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ question }}
                            </li>
                        </ul>
                    </div>

                    <!-- Topics to Study -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-lg font-semibold text-cyan-400">
                            Topics to Study
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(topic, index) in assessment
                                    .interviewPreparation.topicsToStudy"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ topic }}
                            </li>
                        </ul>
                    </div>

                    <!-- Stories to Prepare -->
                    <div>
                        <h4 class="mb-3 text-lg font-semibold text-cyan-400">
                            Stories to Prepare (STAR Method)
                        </h4>
                        <ul class="space-y-2 pl-6">
                            <li
                                v-for="(story, index) in assessment
                                    .interviewPreparation.storiesToPrepare"
                                :key="index"
                                class="list-disc text-base leading-relaxed text-gray-300"
                            >
                                {{ story }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Personalized Recommendation -->
                <div
                    v-if="assessment.personalizedRecommendation"
                    class="border-2 border-[#e900ff] bg-[#e900ff] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Your Action Plan
                    </h3>
                    <div class="space-y-4 text-white">
                        <div>
                            <h4 class="mb-2 text-lg font-semibold">
                                Should You Apply?
                            </h4>
                            <p class="leading-relaxed">
                                {{
                                    assessment.personalizedRecommendation
                                        .shouldApply
                                }}
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 text-lg font-semibold">
                                Your Biggest Advantage
                            </h4>
                            <p class="leading-relaxed">
                                {{
                                    assessment.personalizedRecommendation
                                        .biggestAdvantage
                                }}
                            </p>
                        </div>
                        <div>
                            <h4 class="mb-2 text-lg font-semibold">
                                Next Step (Do This Today)
                            </h4>
                            <p class="leading-relaxed font-semibold">
                                {{
                                    assessment.personalizedRecommendation
                                        .nextStep
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
