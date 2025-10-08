<script setup lang="ts">
import RadarChart from '@/components/charts/RadarChart.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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
    if (importance >= 80) return 'High Priority';
    if (importance >= 50) return 'Medium Priority';
    return 'Low Priority';
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
        <!-- Company Header Banner -->
        <div
            class="bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-8 text-white"
        >
            <div class="container mx-auto">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-lg bg-white"
                    >
                        <Briefcase class="h-8 w-8 text-blue-600" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">
                            {{ analysis.company }}
                        </h1>
                        <p class="text-blue-100">
                            Transforming the digital landscape
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6 py-8">
            <!-- Job Title and Details -->
            <div class="mb-8">
                <Link :href="home.url()">
                    <Button variant="ghost" size="sm" class="mb-4">
                        ← Back to Analysis
                    </Button>
                </Link>

                <h2
                    class="mb-4 text-3xl font-bold text-gray-900 dark:text-white"
                >
                    {{ analysis.jobTitle }}
                </h2>

                <!-- Job Details Bar -->
                <div
                    class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400"
                >
                    <div class="flex items-center gap-2">
                        <Briefcase class="h-4 w-4" />
                        <span>{{ analysis.jobType }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Monitor class="h-4 w-4" />
                        <span>Remote / Hybrid</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <MapPin class="h-4 w-4" />
                        <span>{{ analysis.location }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <DollarSign class="h-4 w-4" />
                        <span>{{ analysis.salaryRange }}</span>
                    </div>
                </div>
            </div>

            <!-- About the Role -->
            <section class="mb-12">
                <h3
                    class="mb-4 text-xl font-bold text-gray-900 dark:text-white"
                >
                    About the Role
                </h3>
                <p class="leading-relaxed text-gray-700 dark:text-gray-300">
                    {{ analysis.summary }}
                </p>
            </section>

            <!-- Skills and Experience Requirements -->
            <div class="mb-12 grid gap-8 lg:grid-cols-2">
                <!-- Required Skills (Left) -->
                <section>
                    <h3
                        class="mb-6 text-xl font-bold text-gray-900 dark:text-white"
                    >
                        Required Skills
                    </h3>
                    <div v-if="analysis.skills.length > 0" class="mb-6">
                        <RadarChart :skills="analysis.skills" />
                        <div
                            class="mt-4 flex items-center justify-center gap-4 text-sm"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-3 w-3 rounded-full bg-blue-500"
                                ></div>
                                <span class="text-gray-600 dark:text-gray-400"
                                    >Required Level</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-3 w-3 rounded-full bg-gray-300"
                                ></div>
                                <span class="text-gray-600 dark:text-gray-400"
                                    >Industry Average</span
                                >
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Experience Requirements (Right) -->
                <section>
                    <h3
                        class="mb-6 text-xl font-bold text-gray-900 dark:text-white"
                    >
                        Experience Requirements
                    </h3>
                    <div class="space-y-6">
                        <div
                            v-for="(req, index) in analysis.requirements"
                            :key="index"
                        >
                            <div class="mb-2 flex items-center justify-between">
                                <span
                                    class="font-medium text-gray-900 dark:text-white"
                                    >{{ req.title }}</span
                                >
                                <span
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                    >{{ req.priority }}%</span
                                >
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                            >
                                <div
                                    :class="getRequirementColor(index)"
                                    class="h-full transition-all duration-500"
                                    :style="{ width: `${req.priority}%` }"
                                />
                            </div>
                        </div>

                        <!-- Additional Requirements -->
                        <div
                            v-if="analysis.skills.length > 0"
                            class="mt-8 rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        >
                            <h4
                                class="mb-3 font-semibold text-gray-900 dark:text-white"
                            >
                                Additional Requirements
                            </h4>
                            <ul
                                class="space-y-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                <li
                                    v-for="skill in analysis.skills.slice(0, 5)"
                                    :key="skill.name"
                                    class="flex items-start gap-2"
                                >
                                    <span class="text-gray-400">•</span>
                                    <span
                                        >{{ skill.name }} ({{ skill.level }}%
                                        proficiency)</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Key Responsibilities -->
            <section class="mb-12">
                <h3
                    class="mb-6 text-xl font-bold text-gray-900 dark:text-white"
                >
                    Key Responsibilities
                </h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="(
                            responsibility, index
                        ) in analysis.responsibilities"
                        :key="index"
                        class="border-l-4 border-gray-300 dark:border-gray-600"
                    >
                        <CardContent class="p-6">
                            <div class="mb-3 flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <CheckCircle2
                                        class="mt-1 h-5 w-5 shrink-0 text-green-500"
                                    />
                                    <h4
                                        class="font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ responsibility.title }}
                                    </h4>
                                </div>
                            </div>
                            <p
                                class="mb-3 text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ responsibility.description }}
                            </p>
                            <Badge
                                :class="
                                    getPriorityColor(responsibility.importance)
                                "
                                class="text-xs"
                            >
                                {{
                                    getPriorityLabel(responsibility.importance)
                                }}
                            </Badge>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- Benefits (if any) -->
            <section v-if="analysis.benefits.length > 0" class="mb-12">
                <h3
                    class="mb-6 text-xl font-bold text-gray-900 dark:text-white"
                >
                    Benefits & Perks
                </h3>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(benefit, index) in analysis.benefits"
                        :key="index"
                        class="flex items-center gap-3 rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                    >
                        <CheckCircle2 class="h-5 w-5 shrink-0 text-green-500" />
                        <span
                            class="text-sm text-gray-700 dark:text-gray-300"
                            >{{ benefit }}</span
                        >
                    </div>
                </div>
            </section>

            <!-- Action Button -->
            <div class="flex justify-center">
                <Link :href="home.url()">
                    <Button size="lg" class="bg-blue-600 hover:bg-blue-700">
                        Analyze Another Job
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
