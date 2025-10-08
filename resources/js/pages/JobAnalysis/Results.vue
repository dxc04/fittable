<script setup lang="ts">
import BarChart from '@/components/charts/BarChart.vue';
import RadarChart from '@/components/charts/RadarChart.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Skill {
    name: string;
    level: number;
    category: string;
}

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
    location: string;
    jobType: string;
    summary: string;
    skills: Skill[];
    responsibilities: Responsibility[];
    requirements: Requirement[];
    benefits: string[];
    salaryRange: string;
}

const props = defineProps<{
    analysis: Analysis;
    originalText: string;
}>();

const technicalSkills = computed(() =>
    props.analysis.skills.filter((s) => s.category === 'technical'),
);

const softSkills = computed(() =>
    props.analysis.skills.filter((s) => s.category === 'soft'),
);

const domainSkills = computed(() =>
    props.analysis.skills.filter((s) => s.category === 'domain'),
);

const mustHaveRequirements = computed(() =>
    props.analysis.requirements
        .filter((r) => r.type === 'must-have')
        .sort((a, b) => b.priority - a.priority),
);

const niceToHaveRequirements = computed(() =>
    props.analysis.requirements
        .filter((r) => r.type === 'nice-to-have')
        .sort((a, b) => b.priority - a.priority),
);

const getCategoryColor = (category: string) => {
    switch (category) {
        case 'technical':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        case 'soft':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'domain':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
};
</script>

<template>
    <Head :title="`Analysis: ${analysis.jobTitle}`" />

    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
    >
        <div class="container mx-auto px-4 py-12">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="route('home')">
                    <Button variant="ghost" size="sm" class="mb-4">
                        ← Analyze Another Job
                    </Button>
                </Link>
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1
                            class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ analysis.jobTitle }}
                        </h1>
                        <div
                            class="mt-2 flex flex-wrap gap-3 text-lg text-gray-600 dark:text-gray-300"
                        >
                            <span class="flex items-center gap-1">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    />
                                </svg>
                                {{ analysis.company }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                                {{ analysis.location }}
                            </span>
                            <Badge variant="secondary">{{
                                analysis.jobType
                            }}</Badge>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-muted-foreground">
                            Salary Range
                        </div>
                        <div
                            class="text-2xl font-bold text-green-600 dark:text-green-400"
                        >
                            {{ analysis.salaryRange }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <Card class="mb-8">
                <CardHeader>
                    <CardTitle>Role Summary</CardTitle>
                </CardHeader>
                <CardContent>
                    <p
                        class="text-lg leading-relaxed text-gray-700 dark:text-gray-300"
                    >
                        {{ analysis.summary }}
                    </p>
                </CardContent>
            </Card>

            <!-- Skills Section -->
            <Card class="mb-8">
                <CardHeader>
                    <CardTitle>Required Skills Analysis</CardTitle>
                    <CardDescription
                        >Visual breakdown of skills by category and proficiency
                        level</CardDescription
                    >
                </CardHeader>
                <CardContent class="space-y-8">
                    <!-- Radar Chart -->
                    <div
                        v-if="analysis.skills.length > 0"
                        class="mx-auto max-w-2xl"
                    >
                        <h3 class="mb-4 text-center text-lg font-semibold">
                            Skills Overview
                        </h3>
                        <RadarChart :skills="analysis.skills" />
                    </div>

                    <Separator />

                    <!-- Skills by Category -->
                    <div class="grid gap-6 md:grid-cols-3">
                        <!-- Technical Skills -->
                        <div v-if="technicalSkills.length > 0">
                            <h4
                                class="mb-3 font-semibold text-blue-600 dark:text-blue-400"
                            >
                                Technical Skills
                            </h4>
                            <div class="space-y-2">
                                <div
                                    v-for="skill in technicalSkills"
                                    :key="skill.name"
                                    class="space-y-1"
                                >
                                    <div
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span>{{ skill.name }}</span>
                                        <span class="text-muted-foreground"
                                            >{{ skill.level }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-full bg-blue-500 transition-all duration-500"
                                            :style="{
                                                width: `${skill.level}%`,
                                            }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Soft Skills -->
                        <div v-if="softSkills.length > 0">
                            <h4
                                class="mb-3 font-semibold text-green-600 dark:text-green-400"
                            >
                                Soft Skills
                            </h4>
                            <div class="space-y-2">
                                <div
                                    v-for="skill in softSkills"
                                    :key="skill.name"
                                    class="space-y-1"
                                >
                                    <div
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span>{{ skill.name }}</span>
                                        <span class="text-muted-foreground"
                                            >{{ skill.level }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-full bg-green-500 transition-all duration-500"
                                            :style="{
                                                width: `${skill.level}%`,
                                            }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Domain Skills -->
                        <div v-if="domainSkills.length > 0">
                            <h4
                                class="mb-3 font-semibold text-purple-600 dark:text-purple-400"
                            >
                                Domain Knowledge
                            </h4>
                            <div class="space-y-2">
                                <div
                                    v-for="skill in domainSkills"
                                    :key="skill.name"
                                    class="space-y-1"
                                >
                                    <div
                                        class="flex items-center justify-between text-sm"
                                    >
                                        <span>{{ skill.name }}</span>
                                        <span class="text-muted-foreground"
                                            >{{ skill.level }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                    >
                                        <div
                                            class="h-full bg-purple-500 transition-all duration-500"
                                            :style="{
                                                width: `${skill.level}%`,
                                            }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Responsibilities Section -->
            <Card class="mb-8">
                <CardHeader>
                    <CardTitle>Key Responsibilities</CardTitle>
                    <CardDescription
                        >Importance ranking of primary job
                        duties</CardDescription
                    >
                </CardHeader>
                <CardContent class="space-y-6">
                    <div
                        v-if="analysis.responsibilities.length > 0"
                        class="mx-auto max-w-4xl"
                    >
                        <BarChart
                            :responsibilities="analysis.responsibilities"
                        />
                    </div>

                    <Separator />

                    <div class="grid gap-4">
                        <div
                            v-for="(
                                responsibility, index
                            ) in analysis.responsibilities"
                            :key="index"
                            class="rounded-lg border bg-card p-4"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <h4 class="font-semibold">
                                    {{ responsibility.title }}
                                </h4>
                                <Badge variant="outline"
                                    >{{ responsibility.importance }}%
                                    importance</Badge
                                >
                            </div>
                            <p class="text-sm text-muted-foreground">
                                {{ responsibility.description }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Requirements Section -->
            <Card class="mb-8">
                <CardHeader>
                    <CardTitle>Requirements Timeline</CardTitle>
                    <CardDescription
                        >Prioritized must-have and nice-to-have
                        qualifications</CardDescription
                    >
                </CardHeader>
                <CardContent class="space-y-6">
                    <!-- Must-Have Requirements -->
                    <div v-if="mustHaveRequirements.length > 0">
                        <h4
                            class="mb-4 flex items-center gap-2 text-lg font-semibold"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-xs font-bold text-red-600 dark:bg-red-900 dark:text-red-200"
                                >!</span
                            >
                            Must-Have Requirements
                        </h4>
                        <div class="space-y-3">
                            <div
                                v-for="(req, index) in mustHaveRequirements"
                                :key="index"
                                class="flex items-start gap-4 rounded-lg border-l-4 border-red-500 bg-card p-4"
                            >
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-sm font-bold text-red-600 dark:bg-red-900 dark:text-red-200"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <h5 class="font-medium">
                                            {{ req.title }}
                                        </h5>
                                        <Badge variant="destructive"
                                            >Priority: {{ req.priority }}</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Separator
                        v-if="
                            mustHaveRequirements.length > 0 &&
                            niceToHaveRequirements.length > 0
                        "
                    />

                    <!-- Nice-to-Have Requirements -->
                    <div v-if="niceToHaveRequirements.length > 0">
                        <h4
                            class="mb-4 flex items-center gap-2 text-lg font-semibold"
                        >
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-600 dark:bg-blue-900 dark:text-blue-200"
                                >+</span
                            >
                            Nice-to-Have Requirements
                        </h4>
                        <div class="space-y-3">
                            <div
                                v-for="(req, index) in niceToHaveRequirements"
                                :key="index"
                                class="flex items-start gap-4 rounded-lg border-l-4 border-blue-500 bg-card p-4"
                            >
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-600 dark:bg-blue-900 dark:text-blue-200"
                                >
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <h5 class="font-medium">
                                            {{ req.title }}
                                        </h5>
                                        <Badge variant="secondary"
                                            >Priority: {{ req.priority }}</Badge
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Benefits Section -->
            <Card v-if="analysis.benefits.length > 0" class="mb-8">
                <CardHeader>
                    <CardTitle>Benefits & Perks</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(benefit, index) in analysis.benefits"
                            :key="index"
                            class="flex items-center gap-3 rounded-lg border bg-card p-3"
                        >
                            <svg
                                class="h-5 w-5 shrink-0 text-green-600 dark:text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <span class="text-sm">{{ benefit }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Action Buttons -->
            <div class="flex justify-center gap-4">
                <Link :href="route('home')">
                    <Button size="lg"> Analyze Another Job </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
