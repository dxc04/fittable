<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as jobIndex } from '@/routes/job';
import { index, show } from '@/routes/job-postings';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Briefcase, Building2, Calendar, MapPin, Plus } from 'lucide-vue-next';

interface JobAnalysis {
    location?: string;
    job_type?: string;
    summary?: string;
}

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
    created_at: string;
    job_analysis?: JobAnalysis;
}

defineProps<{
    jobPostings: JobPosting[];
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Postings',
        href: index().url,
    },
];
</script>

<template>
    <Head title="Job Postings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e] px-6 py-12">
            <div class="mx-auto max-w-6xl">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-white">
                            Job Postings
                        </h1>
                        <p class="text-lg text-gray-400">
                            View all jobs you've analyzed
                        </p>
                    </div>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <Plus class="h-4 w-4" />
                            Analyze New Job
                        </Button>
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-if="jobPostings.length === 0"
                    class="border-2 border-dashed border-gray-700 bg-[#2a2d3e] p-12 text-center"
                >
                    <Briefcase class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                    <h3 class="mb-2 text-xl font-bold text-white">
                        No job postings yet
                    </h3>
                    <p class="mb-6 text-gray-400">
                        Start by analyzing your first job posting
                    </p>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <Plus class="h-4 w-4" />
                            Analyze Job Ad
                        </Button>
                    </Link>
                </div>

                <!-- Job Postings List -->
                <div v-else class="space-y-4">
                    <div
                        v-for="posting in jobPostings"
                        :key="posting.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] p-6 transition-all hover:border-[#e900ff]"
                    >
                        <div class="flex items-start justify-between gap-6">
                            <!-- Left: Job Info -->
                            <div class="flex-1">
                                <div class="mb-3 flex items-start gap-3">
                                    <div
                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center border-2 border-[#e900ff] bg-[#e900ff]/10"
                                    >
                                        <Briefcase
                                            class="h-6 w-6 text-[#e900ff]"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <h3
                                            class="mb-1 text-2xl font-bold text-white"
                                        >
                                            {{ posting.job_title }}
                                        </h3>
                                        <div
                                            class="flex items-center gap-2 text-gray-400"
                                        >
                                            <Building2 class="h-4 w-4" />
                                            <span class="text-lg">{{
                                                posting.company
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Details -->
                                <div
                                    v-if="posting.job_analysis"
                                    class="mb-4 flex flex-wrap gap-4 text-sm text-gray-300"
                                >
                                    <div
                                        v-if="posting.job_analysis.location"
                                        class="flex items-center gap-2"
                                    >
                                        <MapPin
                                            class="h-4 w-4 text-[#e900ff]"
                                        />
                                        <span>{{
                                            posting.job_analysis.location
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="posting.job_analysis.job_type"
                                        class="flex items-center gap-2"
                                    >
                                        <Briefcase
                                            class="h-4 w-4 text-[#e900ff]"
                                        />
                                        <span>{{
                                            posting.job_analysis.job_type
                                        }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Calendar
                                            class="h-4 w-4 text-[#e900ff]"
                                        />
                                        <span>{{
                                            formatDate(posting.created_at)
                                        }}</span>
                                    </div>
                                </div>

                                <!-- Summary -->
                                <p
                                    v-if="posting.job_analysis?.summary"
                                    class="line-clamp-2 text-sm text-gray-300"
                                >
                                    {{ posting.job_analysis.summary }}
                                </p>
                            </div>

                            <!-- Right: Actions -->
                            <div class="flex flex-col gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10"
                                    as-child
                                >
                                    <Link :href="show(posting.id).url">
                                        View Analysis
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
