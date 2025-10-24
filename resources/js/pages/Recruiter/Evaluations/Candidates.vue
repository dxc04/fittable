<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    evaluationsAssessment,
    evaluationsIndex,
    index,
} from '@/routes/recruiter';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, Eye, XCircle } from 'lucide-vue-next';

interface Candidate {
    id: number;
    assessment_id: number;
    resume_id: number;
    overall_match: number;
    summary: string;
    strengths: string[];
    gaps: string[];
    evaluated_at: string;
    resume_preview: string;
}

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
}

const props = defineProps<{
    jobPosting: JobPosting;
    candidates: Candidate[];
}>();

const getMatchColor = (score: number) => {
    if (score >= 80) return 'text-green-400';
    if (score >= 60) return 'text-blue-400';
    return 'text-orange-400';
};

const getMatchBadgeColor = (score: number) => {
    if (score >= 80) return 'bg-green-500 border-green-600';
    if (score >= 60) return 'bg-blue-500 border-blue-600';
    return 'bg-orange-500 border-orange-600';
};

const getMatchLabel = (score: number) => {
    if (score >= 80) return 'Strong Match';
    if (score >= 60) return 'Good Match';
    return 'Needs Review';
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Recruiter',
        href: index().url,
    },
    {
        title: 'My Evaluations',
        href: evaluationsIndex().url,
    },
    {
        title: props.jobPosting.job_title,
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Candidates - ${jobPosting.job_title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="mb-4 flex items-center gap-4">
                        <Link :href="evaluationsIndex.url()">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-white hover:bg-white/20"
                            >
                                <ArrowLeft class="h-4 w-4" />
                            </Button>
                        </Link>
                        <div>
                            <h1 class="mb-1 text-3xl font-bold text-white">
                                {{ jobPosting.job_title }}
                            </h1>
                            <p class="text-sm text-white/80">
                                {{ jobPosting.company }}
                            </p>
                        </div>
                    </div>
                    <p class="text-sm text-white/70">
                        {{ candidates.length }} candidate{{
                            candidates.length !== 1 ? 's' : ''
                        }}
                        evaluated
                    </p>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Candidates List -->
                <div class="space-y-4">
                    <div
                        v-for="(candidate, index) in candidates"
                        :key="candidate.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] transition-all hover:border-[#e900ff]"
                    >
                        <div class="p-6">
                            <!-- Header Row -->
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <!-- Candidate Number -->
                                    <div
                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center border-2 border-gray-600 bg-gray-700 text-lg font-bold text-white"
                                    >
                                        #{{ index + 1 }}
                                    </div>

                                    <!-- Match Score -->
                                    <div>
                                        <div
                                            :class="
                                                getMatchBadgeColor(
                                                    candidate.overall_match,
                                                )
                                            "
                                            class="mb-1 inline-block border-2 px-3 py-1"
                                        >
                                            <span
                                                class="text-2xl font-bold text-white"
                                                >{{
                                                    candidate.overall_match
                                                }}%</span
                                            >
                                        </div>
                                        <p
                                            :class="
                                                getMatchColor(
                                                    candidate.overall_match,
                                                )
                                            "
                                            class="text-sm font-semibold"
                                        >
                                            {{
                                                getMatchLabel(
                                                    candidate.overall_match,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Evaluated Date -->
                                <p class="text-xs text-gray-500">
                                    {{ formatDate(candidate.evaluated_at) }}
                                </p>
                            </div>

                            <!-- Summary -->
                            <div class="mb-4">
                                <h3
                                    class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-400"
                                >
                                    Summary
                                </h3>
                                <p class="text-sm leading-relaxed text-gray-300">
                                    {{ candidate.summary }}
                                </p>
                            </div>

                            <!-- Strengths & Gaps Grid -->
                            <div class="mb-6 grid gap-4 md:grid-cols-2">
                                <!-- Strengths -->
                                <div>
                                    <h3
                                        class="mb-2 flex items-center gap-2 text-sm font-semibold text-green-400"
                                    >
                                        <CheckCircle class="h-4 w-4" />
                                        Key Strengths ({{
                                            candidate.strengths.length
                                        }})
                                    </h3>
                                    <ul class="space-y-1">
                                        <li
                                            v-for="(strength, idx) in candidate.strengths.slice(
                                                0,
                                                3,
                                            )"
                                            :key="idx"
                                            class="text-xs leading-relaxed text-gray-400"
                                        >
                                            • {{ strength }}
                                        </li>
                                        <li
                                            v-if="candidate.strengths.length > 3"
                                            class="text-xs italic text-gray-500"
                                        >
                                            +{{
                                                candidate.strengths.length - 3
                                            }}
                                            more...
                                        </li>
                                    </ul>
                                </div>

                                <!-- Gaps -->
                                <div>
                                    <h3
                                        class="mb-2 flex items-center gap-2 text-sm font-semibold text-orange-400"
                                    >
                                        <XCircle class="h-4 w-4" />
                                        Gaps & Concerns ({{
                                            candidate.gaps.length
                                        }})
                                    </h3>
                                    <ul
                                        v-if="candidate.gaps.length > 0"
                                        class="space-y-1"
                                    >
                                        <li
                                            v-for="(gap, idx) in candidate.gaps.slice(
                                                0,
                                                3,
                                            )"
                                            :key="idx"
                                            class="text-xs leading-relaxed text-gray-400"
                                        >
                                            • {{ gap }}
                                        </li>
                                        <li
                                            v-if="candidate.gaps.length > 3"
                                            class="text-xs italic text-gray-500"
                                        >
                                            +{{ candidate.gaps.length - 3 }}
                                            more...
                                        </li>
                                    </ul>
                                    <p
                                        v-else
                                        class="text-xs italic text-gray-500"
                                    >
                                        No significant gaps identified
                                    </p>
                                </div>
                            </div>

                            <!-- Resume Preview -->
                            <div class="mb-4">
                                <h3
                                    class="mb-2 text-sm font-semibold uppercase tracking-wide text-gray-400"
                                >
                                    Resume Preview
                                </h3>
                                <p
                                    class="border-l-4 border-gray-600 bg-gray-800 px-3 py-2 font-mono text-xs text-gray-400"
                                >
                                    {{ candidate.resume_preview }}
                                </p>
                            </div>

                            <!-- View Full Assessment Button -->
                            <Link
                                :href="
                                    evaluationsAssessment({
                                        jobPosting: jobPosting.id,
                                        assessment: candidate.assessment_id,
                                    }).url
                                "
                            >
                                <Button
                                    class="w-full gap-2 bg-gray-700 text-white transition-colors group-hover:bg-[#e900ff] group-hover:text-white"
                                >
                                    <Eye class="h-4 w-4" />
                                    View Full Assessment
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
