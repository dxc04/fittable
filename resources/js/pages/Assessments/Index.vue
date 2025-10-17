<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/assessments';
import { index as jobIndex } from '@/routes/job';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, TrendingUp } from 'lucide-vue-next';

interface Assessment {
    id: number;
    overall_match: number;
    summary: string;
    created_at: string;
    job_posting?: {
        job_title: string;
        company: string;
    };
}

defineProps<{
    assessments: Assessment[];
}>();

const getMatchLabel = (score: number) => {
    if (score >= 80) return 'Strong Match';
    if (score >= 60) return 'Good Match';
    return 'Needs Improvement';
};

const getMatchColor = (score: number) => {
    if (score >= 80) return 'text-green-400 border-green-400';
    if (score >= 60) return 'text-blue-400 border-blue-400';
    return 'text-orange-400 border-orange-400';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Assessments',
        href: index().url,
    },
];
</script>

<template>
    <Head title="Assessments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e] px-6 py-12">
            <div class="mx-auto max-w-6xl">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-white">
                            Assessments
                        </h1>
                        <p class="text-lg text-gray-400">
                            View all your resume assessments and job matches
                        </p>
                    </div>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <BookOpen class="h-4 w-4" />
                            New Assessment
                        </Button>
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-if="assessments.length === 0"
                    class="border-2 border-dashed border-gray-700 bg-[#2a2d3e] p-12 text-center"
                >
                    <BookOpen class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                    <h3 class="mb-2 text-xl font-bold text-white">
                        No assessments yet
                    </h3>
                    <p class="mb-6 text-gray-400">
                        Start by analyzing a job posting to get your first
                        assessment
                    </p>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <BookOpen class="h-4 w-4" />
                            Analyze Job Ad
                        </Button>
                    </Link>
                </div>

                <!-- Assessments Grid -->
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="assessment in assessments"
                        :key="assessment.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] p-6 transition-all hover:border-[#e900ff]"
                    >
                        <!-- Job Info -->
                        <div class="mb-4">
                            <h3
                                class="mb-1 line-clamp-2 text-xl font-bold text-white"
                            >
                                {{
                                    assessment.job_posting?.job_title ||
                                    'Job Position'
                                }}
                            </h3>
                            <p class="text-sm text-gray-400">
                                {{
                                    assessment.job_posting?.company || 'Company'
                                }}
                            </p>
                        </div>

                        <!-- Match Score -->
                        <div
                            class="mb-4 border-2 p-4 text-center"
                            :class="getMatchColor(assessment.overall_match)"
                        >
                            <div
                                class="mb-1 flex items-center justify-center gap-2"
                            >
                                <TrendingUp class="h-5 w-5" />
                                <span class="text-3xl font-bold">
                                    {{ assessment.overall_match }}%
                                </span>
                            </div>
                            <span class="text-xs font-semibold uppercase">
                                {{ getMatchLabel(assessment.overall_match) }}
                            </span>
                        </div>

                        <!-- Summary -->
                        <p class="mb-4 line-clamp-3 text-sm text-gray-300">
                            {{ assessment.summary }}
                        </p>

                        <!-- Date -->
                        <div
                            class="mb-4 flex items-center gap-2 text-xs text-gray-500"
                        >
                            <Calendar class="h-3 w-3" />
                            {{ formatDate(assessment.created_at) }}
                        </div>

                        <!-- View Button -->
                        <Button
                            variant="outline"
                            class="w-full border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10"
                            as-child
                        >
                            <Link :href="show(assessment.id).url">
                                View Details
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
