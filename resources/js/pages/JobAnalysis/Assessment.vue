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

interface Assessment {
    overallMatch: number;
    skillBreakdown: SkillBreakdown;
    summary: string;
    strengths: string[];
    gaps: string[];
    recommendation: string;
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

                <!-- Your Strengths & Skills to Develop -->
                <div class="mb-8 grid gap-6 lg:grid-cols-2">
                    <!-- Your Strengths -->
                    <div class="border-2 border-gray-700 bg-[#2a2d3e] p-8">
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Your Strengths
                        </h3>
                        <p class="text-sm leading-relaxed text-gray-300">
                            {{ assessment.strengths.join('. ') }}.
                        </p>
                    </div>

                    <!-- Skills to Highlight or Develop -->
                    <div class="border-2 border-gray-700 bg-[#2a2d3e] p-8">
                        <h3 class="mb-4 text-xl font-bold text-white">
                            Skills to Highlight or Develop
                        </h3>
                        <p class="text-sm leading-relaxed text-gray-300">
                            <template v-if="assessment.gaps.length > 0">
                                While you possess a strong skill set, consider
                                highlighting your experience in the following
                                areas: {{ assessment.gaps.join(', ') }}.
                            </template>
                            <template v-else>
                                Your skills align very well with this position.
                                Focus on emphasizing your strengths in your
                                application materials.
                            </template>
                        </p>
                    </div>
                </div>

                <!-- Missing Skills -->
                <div
                    v-if="assessment.gaps.length > 0"
                    class="mb-8 border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Missing Skills
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-300">
                        Based on the job description, you may need to further
                        develop skills in {{ assessment.gaps.join(', ') }}.
                        Consider taking online courses or working on projects to
                        gain experience in these areas.
                    </p>
                </div>

                <!-- Our Recommendation -->
                <div class="border-2 border-[#e900ff] bg-[#e900ff] p-8">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Our Recommendation
                    </h3>
                    <p class="text-sm leading-relaxed text-white">
                        <template v-if="assessment.overallMatch >= 80">
                            Given your strong match score and relevant
                            experience, we recommend applying for this position.
                            Tailor your resume to emphasize your strengths and
                            address any potential skill gaps. Prepare examples
                            of your work that demonstrate your technical
                            proficiency, communication abilities, and
                            problem-solving skills.
                        </template>
                        <template v-else-if="assessment.overallMatch >= 60">
                            You're a good candidate for this position. Highlight
                            your key strengths in your application and be
                            prepared to discuss how you can address any skill
                            gaps through learning and development.
                        </template>
                        <template v-else>
                            Consider building more experience in the required
                            areas before applying. Focus on developing the
                            missing skills through courses, projects, or related
                            positions.
                        </template>
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
