<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { show as showAssessment } from '@/routes/assessments';
import { index } from '@/routes/resumes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, FileText, TrendingUp } from 'lucide-vue-next';

interface JobPosting {
    id: number;
    job_title: string;
    company: string;
}

interface Assessment {
    id: number;
    overall_match: number;
    created_at: string;
    job_posting?: JobPosting;
}

interface Resume {
    id: number;
    resume_text: string;
    created_at: string;
    assessments_count: number;
    assessments: Assessment[];
}

const props = defineProps<{
    resume: Resume;
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
        title: 'Resumes',
        href: index().url,
    },
    {
        title: `Resume #${props.resume.id}`,
        href: '#',
    },
];
</script>

<template>
    <Head :title="`Resume #${resume.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e]">
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-8">
                <div class="container mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="mb-2 text-3xl font-bold text-white">
                                Resume #{{ resume.id }}
                            </h1>
                            <div
                                class="flex items-center gap-4 text-sm text-gray-400"
                            >
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4" />
                                    <span>{{
                                        formatDate(resume.created_at)
                                    }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <TrendingUp class="h-4 w-4" />
                                    <span
                                        >{{
                                            resume.assessments_count
                                        }}
                                        Assessment{{
                                            resume.assessments_count !== 1
                                                ? 's'
                                                : ''
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                        <Link :href="index().url">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="text-white hover:bg-white/20"
                            >
                                <ArrowLeft class="mr-2 h-4 w-4" />
                                Back to List
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 py-8">
                <!-- Resume Content -->
                <div class="mb-8 border-2 border-[#e900ff] bg-[#2a2d3e] p-8">
                    <div class="mb-4 flex items-center gap-2">
                        <FileText class="h-6 w-6 text-[#e900ff]" />
                        <h2 class="text-2xl font-bold text-white">
                            Resume Content
                        </h2>
                    </div>
                    <div
                        class="rounded bg-[#1a1d2e] p-6 font-mono text-sm leading-relaxed break-words whitespace-pre-wrap text-gray-300"
                    >
                        {{ resume.resume_text }}
                    </div>
                </div>

                <!-- Assessments -->
                <div
                    v-if="resume.assessments.length > 0"
                    class="border-2 border-gray-700 bg-[#2a2d3e] p-8"
                >
                    <h2 class="mb-6 text-2xl font-bold text-white">
                        Related Assessments
                    </h2>
                    <div class="space-y-4">
                        <div
                            v-for="assessment in resume.assessments"
                            :key="assessment.id"
                            class="border-l-4 border-[#e900ff] bg-[#1a1d2e] p-4 transition-all hover:bg-[#1a1d2e]/80"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3
                                        class="mb-1 text-lg font-bold text-white"
                                    >
                                        {{
                                            assessment.job_posting?.job_title ||
                                            'Job Position'
                                        }}
                                    </h3>
                                    <p class="mb-2 text-sm text-gray-400">
                                        {{
                                            assessment.job_posting?.company ||
                                            'Company'
                                        }}
                                    </p>
                                    <div class="flex items-center gap-4">
                                        <Badge
                                            variant="outline"
                                            class="border-[#e900ff] text-[#e900ff]"
                                        >
                                            Match:
                                            {{ assessment.overall_match }}%
                                        </Badge>
                                        <span class="text-xs text-gray-500">
                                            {{
                                                formatDate(
                                                    assessment.created_at,
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10"
                                    as-child
                                >
                                    <Link
                                        :href="
                                            showAssessment(assessment.id).url
                                        "
                                    >
                                        View Assessment
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Assessments -->
                <div
                    v-else
                    class="border-2 border-dashed border-gray-700 bg-[#2a2d3e] p-12 text-center"
                >
                    <TrendingUp class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                    <h3 class="mb-2 text-xl font-bold text-white">
                        No assessments yet
                    </h3>
                    <p class="text-gray-400">
                        This resume hasn't been used for any job assessments
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
