<script setup lang="ts">
import RadarChart from '@/components/charts/RadarChart.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index } from '@/routes/job';
import { Head, Link } from '@inertiajs/vue3';
import {
    Briefcase,
    CheckCircle,
    Clock,
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
    if (importance >= 80) return 'bg-red-50 text-red-700 border-red-200';
    if (importance >= 50) return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    return 'bg-green-50 text-green-700 border-green-200';
};

const getCheckColor = (importance: number) => {
    if (importance >= 80) return 'text-red-600';
    if (importance >= 50) return 'text-yellow-600';
    return 'text-green-600';
};

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
</script>

<template>
    <Head :title="`Analysis: ${analysis.jobTitle}`" />

    <div class="min-h-screen bg-gray-100">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-blue-500 via-blue-400 to-cyan-400 px-6 py-8">
            <div class="container mx-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white p-3 shadow-lg"
                        >
                            <Briefcase class="h-10 w-10 text-gray-800" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white">
                                {{ analysis.company }}
                            </h1>
                            <p class="text-sm text-blue-100">
                                Transforming the digital landscape
                            </p>
                        </div>
                    </div>
                    <Link :href="index.url()">
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

        <div class="container mx-auto px-6 py-8">
            <!-- Job Title Section -->
            <div class="mb-6 rounded-3xl bg-white p-6 shadow-md">
                <h2 class="mb-3 text-3xl font-bold text-gray-900">
                    {{ analysis.jobTitle }}
                </h2>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-1.5">
                        <Briefcase class="h-4 w-4" />
                        <span>{{ analysis.jobType }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <Clock class="h-4 w-4" />
                        <span>Remote / Hybrid</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <MapPin class="h-4 w-4" />
                        <span>{{ analysis.location }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <DollarSign class="h-4 w-4" />
                        <span>{{ analysis.salaryRange }}</span>
                    </div>
                </div>
            </div>

            <!-- About the Role -->
            <div class="mb-6 rounded-3xl bg-white p-6 shadow-md">
                <h3 class="mb-3 text-xl font-bold text-gray-900">About the Role</h3>
                <p class="text-sm leading-relaxed text-gray-600">
                    {{ analysis.summary }}
                </p>
            </div>

            <!-- Two Column Layout: Skills & Requirements -->
            <div class="mb-6 grid gap-6 lg:grid-cols-2">
                <!-- Skills Radar Chart -->
                <div class="rounded-3xl bg-indigo-500 p-6 shadow-md">
                    <h3 class="mb-4 text-xl font-bold text-white">Required Skills</h3>
                    <div v-if="analysis.skills.length > 0" class="rounded-2xl bg-white p-6">
                        <RadarChart :skills="analysis.skills" />
                        <div class="mt-4 flex items-center justify-center gap-4 text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="h-3 w-3 rounded-md bg-blue-500"></div>
                                <span class="text-gray-700">Required Level</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="h-3 w-3 rounded-md bg-gray-300"></div>
                                <span class="text-gray-700">Industry Average</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Experience Requirements -->
                <div class="rounded-3xl bg-teal-500 p-6 shadow-md">
                    <h3 class="mb-4 text-xl font-bold text-white">
                        Experience Requirements
                    </h3>
                    <div class="space-y-4">
                        <div v-for="(req, index) in mainRequirements" :key="index">
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="font-semibold text-white">{{ req.title }}</span>
                                <span class="text-xs text-teal-100">{{
                                    req.type === 'must-have'
                                        ? req.priority + '%'
                                        : req.priority / 10 + '+ years'
                                }}</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-teal-600">
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
                            class="mt-6 rounded-2xl bg-white p-4"
                        >
                            <h4 class="mb-3 font-semibold text-gray-900">
                                Additional Requirements
                            </h4>
                            <ul class="space-y-2">
                                <li
                                    v-for="(req, index) in additionalRequirements"
                                    :key="index"
                                    class="flex items-start gap-2 text-sm text-gray-700"
                                >
                                    <span
                                        class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-gray-400"
                                    ></span>
                                    <span>{{ req.title }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Responsibilities -->
            <div class="mb-6 rounded-3xl bg-white p-6 shadow-md">
                <h3 class="mb-4 text-xl font-bold text-gray-900">Key Responsibilities</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="(responsibility, index) in analysis.responsibilities"
                        :key="index"
                        class="flex flex-col gap-3 rounded-2xl border-2 border-gray-100 bg-gray-50 p-5 transition-all hover:border-gray-200 hover:shadow-md"
                    >
                        <div class="flex items-start gap-3">
                            <CheckCircle
                                :class="getCheckColor(responsibility.importance)"
                                class="mt-0.5 h-5 w-5 shrink-0"
                            />
                            <div class="flex-1">
                                <h4 class="mb-1 text-base font-semibold text-gray-900">
                                    {{ responsibility.title }}
                                </h4>
                                <p class="text-sm leading-relaxed text-gray-600">
                                    {{ responsibility.description }}
                                </p>
                            </div>
                        </div>
                        <Badge
                            :class="getPriorityColor(responsibility.importance)"
                            class="w-fit rounded-full border-2 px-3 py-1 text-xs font-medium"
                        >
                            {{ getPriorityLabel(responsibility.importance) }}
                        </Badge>
                    </div>
                </div>
            </div>

            <!-- Skills Breakdown & Benefits -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Skills Breakdown -->
                <div v-if="analysis.skills.length > 0" class="rounded-3xl bg-orange-500 p-6 shadow-md">
                    <h3 class="mb-4 text-xl font-bold text-white">Skills Breakdown</h3>
                    <div class="space-y-3 rounded-2xl bg-white p-5">
                        <div
                            v-for="skill in analysis.skills"
                            :key="skill.name"
                            class="flex items-center justify-between text-sm"
                        >
                            <span class="font-medium text-gray-900">{{ skill.name }}</span>
                            <div class="flex items-center gap-2">
                                <div class="h-2.5 w-24 overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        class="h-full rounded-full bg-orange-500"
                                        :style="{
                                            width: `${skill.level}%`,
                                        }"
                                    />
                                </div>
                                <span class="w-10 text-right text-xs text-gray-500"
                                    >{{ skill.level }}%</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Benefits -->
                <div v-if="analysis.benefits.length > 0" class="rounded-3xl bg-emerald-500 p-6 shadow-md">
                    <h3 class="mb-4 text-xl font-bold text-white">Benefits & Perks</h3>
                    <div class="space-y-2.5 rounded-2xl bg-white p-5">
                        <div
                            v-for="(benefit, index) in analysis.benefits"
                            :key="index"
                            class="flex items-start gap-2 text-sm"
                        >
                            <CheckCircle class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />
                            <span class="text-gray-700">{{ benefit }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
