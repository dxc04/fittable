<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import recruiter from '@/routes/recruiter';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Briefcase, ChevronRight, TrendingUp, Users } from 'lucide-vue-next';

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
    assessment_count: number;
    average_match_score: number;
    highest_match_score: number;
    latest_evaluation_date: string;
    job_analysis: {
        location: string | null;
        job_type: string | null;
        summary: string | null;
    } | null;
}

defineProps<{
    jobPostings: JobPosting[];
}>();

const getMatchColor = (score: number) => {
    if (score >= 80) return 'text-green-400';
    if (score >= 60) return 'text-blue-400';
    return 'text-orange-400';
};

const getMatchBadgeColor = (score: number) => {
    if (score >= 80)
        return 'bg-green-500/20 text-green-400 border-green-500/50';
    if (score >= 60) return 'bg-blue-500/20 text-blue-400 border-blue-500/50';
    return 'bg-orange-500/20 text-orange-400 border-orange-500/50';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Recruiter',
        href: recruiter.index().url,
    },
    {
        title: 'My Evaluations',
        href: '#',
    },
];
</script>

<template>
    <Head title="My Evaluations - Recruiter" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="mb-2 text-3xl font-bold text-white">
                                My Evaluations
                            </h1>
                            <p class="text-sm text-white/90">
                                Review candidates you've evaluated grouped by
                                job posting
                            </p>
                        </div>
                        <Link :href="recruiter.index.url()">
                            <Button
                                variant="outline"
                                class="border-[#e900ff] bg-[#e900ff]/10 text-[#e900ff] hover:bg-[#e900ff]/20"
                            >
                                <TrendingUp class="mr-2 h-4 w-4" />
                                New Match
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Empty State -->
                <div
                    v-if="jobPostings.length === 0"
                    class="flex flex-col items-center justify-center border-2 border-dashed border-gray-600 bg-[#2a2d3e] px-6 py-16 text-center"
                >
                    <Users class="mb-4 h-16 w-16 text-gray-500" />
                    <h2 class="mb-2 text-xl font-semibold text-white">
                        No Evaluations Yet
                    </h2>
                    <p class="mb-6 text-sm text-gray-400">
                        Start evaluating candidates to see them appear here
                    </p>
                    <Link :href="recruiter.index.url()">
                        <Button
                            class="bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <TrendingUp class="mr-2 h-4 w-4" />
                            Evaluate First Candidate
                        </Button>
                    </Link>
                </div>

                <!-- Job Postings Grid -->
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="job in jobPostings"
                        :key="job.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] transition-all hover:border-[#e900ff]"
                    >
                        <div class="p-6">
                            <!-- Job Header -->
                            <div class="mb-4 flex items-start gap-3">
                                <div
                                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center bg-[#e900ff]/20"
                                >
                                    <Briefcase class="h-6 w-6 text-[#e900ff]" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3
                                        class="mb-1 truncate text-lg font-semibold text-white"
                                    >
                                        {{ job.job_title }}
                                    </h3>
                                    <p class="truncate text-sm text-gray-400">
                                        {{ job.company }}
                                    </p>
                                </div>
                            </div>

                            <!-- Job Details -->
                            <div v-if="job.job_analysis" class="mb-4 space-y-1">
                                <p
                                    v-if="job.job_analysis.location"
                                    class="text-xs text-gray-400"
                                >
                                    {{ job.job_analysis.location }}
                                </p>
                                <p
                                    v-if="job.job_analysis.job_type"
                                    class="text-xs text-gray-400"
                                >
                                    {{ job.job_analysis.job_type }}
                                </p>
                            </div>

                            <!-- Stats -->
                            <div class="mb-4 space-y-3">
                                <!-- Candidates Evaluated -->
                                <div
                                    class="flex items-center justify-between border-b border-gray-700 pb-2"
                                >
                                    <span class="text-sm text-gray-400"
                                        >Candidates Evaluated</span
                                    >
                                    <span
                                        class="text-lg font-bold text-[#e900ff]"
                                        >{{ job.assessment_count }}</span
                                    >
                                </div>

                                <!-- Average Match -->
                                <div
                                    class="flex items-center justify-between border-b border-gray-700 pb-2"
                                >
                                    <span class="text-sm text-gray-400"
                                        >Avg Match</span
                                    >
                                    <span
                                        :class="
                                            getMatchColor(
                                                job.average_match_score,
                                            )
                                        "
                                        class="text-lg font-bold"
                                        >{{
                                            Math.round(job.average_match_score)
                                        }}%</span
                                    >
                                </div>

                                <!-- Highest Match -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400"
                                        >Highest Match</span
                                    >
                                    <span
                                        :class="
                                            getMatchBadgeColor(
                                                job.highest_match_score,
                                            )
                                        "
                                        class="border px-2 py-1 text-sm font-bold"
                                        >{{ job.highest_match_score }}%</span
                                    >
                                </div>
                            </div>

                            <!-- Latest Evaluation Date -->
                            <p class="mb-4 text-xs text-gray-500">
                                Last evaluated:
                                {{ formatDate(job.latest_evaluation_date) }}
                            </p>

                            <!-- View Candidates Button -->
                            <Link
                                :href="
                                    recruiter.evaluations.candidates({
                                        jobPosting: job.id,
                                    }).url
                                "
                            >
                                <Button
                                    class="w-full gap-2 bg-gray-700 text-white transition-colors group-hover:bg-[#e900ff] group-hover:text-white"
                                >
                                    View Candidates
                                    <ChevronRight class="h-4 w-4" />
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
