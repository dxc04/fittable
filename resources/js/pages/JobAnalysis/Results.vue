<script setup lang="ts">
import RadarChart from '@/components/charts/RadarChart.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { home } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import {
    Briefcase,
    CheckCircle2,
    DollarSign,
    MapPin,
    Monitor,
} from 'lucide-vue-next';

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

const getPriorityLabel = (importance: number) => {
    if (importance >= 80) return 'High';
    if (importance >= 50) return 'Med';
    return 'Low';
};

const getPriorityColor = (importance: number) => {
    if (importance >= 80)
        return 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300';
    if (importance >= 50)
        return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300';
    return 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300';
};

const getRequirementColor = (index: number) => {
    const colors = [
        'bg-blue-500',
        'bg-purple-500',
        'bg-cyan-500',
        'bg-teal-500',
        'bg-orange-500',
    ];
    return colors[index % colors.length];
};
</script>

<template>
    <Head :title="`Analysis: ${analysis.jobTitle}`" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Compact Header -->
        <div
            class="border-b bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-4 text-white"
        >
            <div class="container mx-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-white"
                        >
                            <Briefcase class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">
                                {{ analysis.jobTitle }}
                            </h1>
                            <div
                                class="flex items-center gap-3 text-xs text-blue-100"
                            >
                                <span>{{ analysis.company }}</span>
                                <span>•</span>
                                <span>{{ analysis.location }}</span>
                                <span>•</span>
                                <span>{{ analysis.salaryRange }}</span>
                            </div>
                        </div>
                    </div>
                    <Link :href="home.url()">
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-white hover:bg-white/20"
                        >
                            ← Back
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6">
            <!-- Main Grid Layout -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Left Column: Summary & Skills -->
                <div class="space-y-4 lg:col-span-1">
                    <!-- Summary Card -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >About the Role</CardTitle
                            >
                        </CardHeader>
                        <CardContent
                            class="text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ analysis.summary }}
                        </CardContent>
                    </Card>

                    <!-- Skills Radar Chart -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >Required Skills</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <div v-if="analysis.skills.length > 0">
                                <RadarChart :skills="analysis.skills" />
                                <div
                                    class="mt-2 flex items-center justify-center gap-3 text-xs"
                                >
                                    <div class="flex items-center gap-1">
                                        <div
                                            class="h-2 w-2 rounded-full bg-blue-500"
                                        ></div>
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Required</span
                                        >
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <div
                                            class="h-2 w-2 rounded-full bg-gray-300"
                                        ></div>
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Industry Avg</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Middle Column: Requirements & Responsibilities -->
                <div class="space-y-4 lg:col-span-1">
                    <!-- Experience Requirements -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >Experience Requirements</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div
                                v-for="(req, index) in analysis.requirements"
                                :key="index"
                            >
                                <div
                                    class="mb-1 flex items-center justify-between text-sm"
                                >
                                    <span
                                        class="font-medium text-gray-900 dark:text-white"
                                        >{{ req.title }}</span
                                    >
                                    <span
                                        class="text-xs text-gray-600 dark:text-gray-400"
                                        >{{ req.priority }}%</span
                                    >
                                </div>
                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                >
                                    <div
                                        :class="getRequirementColor(index)"
                                        class="h-full transition-all duration-500"
                                        :style="{ width: `${req.priority}%` }"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Compact Responsibilities -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >Key Responsibilities</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-2">
                            <div
                                v-for="(
                                    responsibility, index
                                ) in analysis.responsibilities.slice(0, 4)"
                                :key="index"
                                class="flex items-start gap-2 rounded-md border p-2 dark:border-gray-700"
                            >
                                <CheckCircle2
                                    class="mt-0.5 h-4 w-4 shrink-0 text-green-500"
                                />
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ responsibility.title }}
                                    </h4>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        {{ responsibility.description }}
                                    </p>
                                </div>
                                <Badge
                                    :class="
                                        getPriorityColor(
                                            responsibility.importance,
                                        )
                                    "
                                    class="shrink-0 text-xs"
                                >
                                    {{
                                        getPriorityLabel(
                                            responsibility.importance,
                                        )
                                    }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Additional Info -->
                <div class="space-y-4 lg:col-span-1">
                    <!-- Skills List -->
                    <Card v-if="analysis.skills.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >Skills Breakdown</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <div
                                    v-for="skill in analysis.skills"
                                    :key="skill.name"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span
                                        class="text-gray-900 dark:text-white"
                                        >{{ skill.name }}</span
                                    >
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-16 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                        >
                                            <div
                                                class="h-full bg-blue-500"
                                                :style="{
                                                    width: `${skill.level}%`,
                                                }"
                                            />
                                        </div>
                                        <span
                                            class="w-10 text-right text-xs text-gray-600 dark:text-gray-400"
                                            >{{ skill.level }}%</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Benefits -->
                    <Card v-if="analysis.benefits.length > 0">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base"
                                >Benefits & Perks</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-1.5">
                                <div
                                    v-for="(
                                        benefit, index
                                    ) in analysis.benefits"
                                    :key="index"
                                    class="flex items-start gap-2 text-sm"
                                >
                                    <CheckCircle2
                                        class="mt-0.5 h-4 w-4 shrink-0 text-green-500"
                                    />
                                    <span
                                        class="text-gray-700 dark:text-gray-300"
                                        >{{ benefit }}</span
                                    >
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Job Details -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Job Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-2 text-sm">
                            <div
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                            >
                                <Briefcase class="h-4 w-4 text-gray-400" />
                                <span>{{ analysis.jobType }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                            >
                                <Monitor class="h-4 w-4 text-gray-400" />
                                <span>Remote / Hybrid</span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                            >
                                <MapPin class="h-4 w-4 text-gray-400" />
                                <span>{{ analysis.location }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300"
                            >
                                <DollarSign class="h-4 w-4 text-gray-400" />
                                <span>{{ analysis.salaryRange }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
