<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/recruiter';
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

const props = defineProps<{
    assessment: Assessment;
    jobDescription: string;
    candidateResume: string;
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
        title: 'Recruiter',
        href: index().url,
    },
    {
        title: 'Match Result',
        href: '#',
    },
];
</script>

<template>
    <Head title="Candidate Match Result - Recruiter" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="mb-2 text-3xl font-bold text-white">
                                Candidate Match Result
                            </h1>
                            <p class="text-sm text-white/90">
                                AI-powered assessment of candidate fit
                            </p>
                        </div>
                        <Link :href="index.url()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-white hover:bg-white/20"
                            >
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                New Match
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
                        Candidate Match Score
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

                <!-- Candidate Strengths -->
                <div class="mb-8 border-2 border-green-500 bg-[#2a2d3e] p-8">
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
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
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
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

                <!-- Interview Strategy (if available) -->
                <div
                    v-if="assessment.interviewPreparation"
                    class="mb-8 border-2 border-cyan-500 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Interview Strategy
                    </h3>

                    <!-- Likely Questions -->
                    <div class="mb-6">
                        <h4 class="mb-3 text-lg font-semibold text-cyan-400">
                            Key Questions to Ask
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

                    <!-- Topics to Assess -->
                    <div>
                        <h4 class="mb-3 text-lg font-semibold text-cyan-400">
                            Topics to Assess During Interview
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
                </div>

                <!-- Hiring Recommendation -->
                <div
                    v-if="assessment.personalizedRecommendation"
                    class="border-2 border-[#e900ff] bg-[#e900ff] p-8"
                >
                    <h3 class="mb-6 text-2xl font-bold text-white uppercase">
                        Hiring Recommendation
                    </h3>
                    <div class="space-y-4 text-white">
                        <div>
                            <h4 class="mb-2 text-lg font-semibold">
                                Should You Interview?
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
                                Candidate's Biggest Advantage
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
                                Next Step
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
