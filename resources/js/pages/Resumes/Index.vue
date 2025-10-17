<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as jobIndex } from '@/routes/job';
import { index, show } from '@/routes/resumes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, FileText, Plus, TrendingUp } from 'lucide-vue-next';

interface Resume {
    id: number;
    resume_text: string;
    created_at: string;
    assessments_count: number;
}

defineProps<{
    resumes: Resume[];
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const getExcerpt = (text: string, maxLength: number = 150) => {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Resumes',
        href: index().url,
    },
];
</script>

<template>
    <Head title="Resumes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e] px-6 py-12">
            <div class="mx-auto max-w-6xl">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-white">
                            Resumes
                        </h1>
                        <p class="text-lg text-gray-400">
                            View all your uploaded resumes and their assessments
                        </p>
                    </div>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <Plus class="h-4 w-4" />
                            New Assessment
                        </Button>
                    </Link>
                </div>

                <!-- Empty State -->
                <div
                    v-if="resumes.length === 0"
                    class="border-2 border-dashed border-gray-700 bg-[#2a2d3e] p-12 text-center"
                >
                    <FileText class="mx-auto mb-4 h-16 w-16 text-gray-600" />
                    <h3 class="mb-2 text-xl font-bold text-white">
                        No resumes yet
                    </h3>
                    <p class="mb-6 text-gray-400">
                        Upload your first resume by creating an assessment
                    </p>
                    <Link :href="jobIndex().url">
                        <Button
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        >
                            <Plus class="h-4 w-4" />
                            Create Assessment
                        </Button>
                    </Link>
                </div>

                <!-- Resumes Grid -->
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="resume in resumes"
                        :key="resume.id"
                        class="group border-2 border-gray-700 bg-[#2a2d3e] p-6 transition-all hover:border-[#e900ff]"
                    >
                        <!-- Icon -->
                        <div class="mb-4 flex items-start justify-between">
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center border-2 border-[#e900ff] bg-[#e900ff]/10"
                            >
                                <FileText class="h-6 w-6 text-[#e900ff]" />
                            </div>
                            <div
                                class="flex items-center gap-2 border border-gray-600 bg-[#1a1d2e] px-3 py-1 text-sm"
                            >
                                <TrendingUp class="h-4 w-4 text-[#e900ff]" />
                                <span class="font-bold text-white">
                                    {{ resume.assessments_count }}
                                </span>
                                <span class="text-gray-400">
                                    Assessment{{
                                        resume.assessments_count !== 1
                                            ? 's'
                                            : ''
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Resume Preview -->
                        <div class="mb-4">
                            <h3 class="mb-2 text-lg font-bold text-white">
                                Resume #{{ resume.id }}
                            </h3>
                            <p class="line-clamp-4 text-sm text-gray-300">
                                {{ getExcerpt(resume.resume_text, 200) }}
                            </p>
                        </div>

                        <!-- Date -->
                        <div
                            class="mb-4 flex items-center gap-2 text-xs text-gray-500"
                        >
                            <Calendar class="h-3 w-3" />
                            {{ formatDate(resume.created_at) }}
                        </div>

                        <!-- View Button -->
                        <Button
                            variant="outline"
                            class="w-full border-[#e900ff] text-[#e900ff] hover:bg-[#e900ff]/10"
                            as-child
                        >
                            <Link :href="show(resume.id).url">
                                View Details
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
