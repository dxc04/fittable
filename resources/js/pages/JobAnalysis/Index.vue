<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { analyze, index } from '@/routes/job';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth.user);

const form = useForm({
    jobTitle: '',
    company: '',
    jobAdText: '',
});

const submit = () => {
    form.post(analyze.url(), {
        preserveScroll: true,
    });
};

const clearForm = () => {
    form.reset();
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Job Analysis',
        href: index().url,
    },
];
</script>

<template>
    <Head title="Job Fit Analysis" />

    <AppLayout v-if="isAuthenticated" :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-[#1a1d2e] px-8 py-12">
            <div class="mx-auto max-w-4xl">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-3 text-4xl font-bold text-white">
                        Analyze Job Ad
                    </h1>
                    <p class="text-lg text-gray-400">
                        Paste the full job posting to match it with your skills.
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Job Title -->
                    <div class="space-y-3">
                        <Label for="jobTitle" class="text-base text-white">
                            Job Title (optional)
                        </Label>
                        <Input
                            id="jobTitle"
                            v-model="form.jobTitle"
                            placeholder="e.g. Senior Frontend Developer"
                            class="border-2 border-[#e900ff] bg-transparent text-white placeholder:text-gray-500"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.jobTitle" />
                    </div>

                    <!-- Job Ad Text -->
                    <div class="space-y-3">
                        <Label for="jobAdText" class="text-base text-white">
                            Paste Job Ad
                        </Label>
                        <Textarea
                            id="jobAdText"
                            v-model="form.jobAdText"
                            placeholder="Paste the full job posting including requirements and qualifications..."
                            rows="12"
                            class="border-2 border-dashed border-[#e900ff] bg-transparent font-mono text-sm text-white placeholder:text-gray-500"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.jobAdText" />
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="clearForm"
                            class="border-2 border-[#e900ff] bg-transparent text-[#e900ff] hover:bg-[#e900ff]/10"
                            :disabled="form.processing"
                        >
                            Clear
                        </Button>
                        <Button
                            type="submit"
                            class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                            :disabled="form.processing || !form.jobAdText"
                        >
                            {{
                                form.processing
                                    ? 'Analyzing...'
                                    : 'Analyze Job Ad'
                            }}
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>

    <div v-else class="min-h-screen bg-[#1a1d2e] px-8 py-12">
        <div class="mx-auto max-w-4xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="mb-3 text-4xl font-bold text-white">
                    Analyze Job Ad
                </h1>
                <p class="text-lg text-gray-400">
                    Paste the full job posting to match it with your skills.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Job Title -->
                <div class="space-y-3">
                    <Label for="jobTitle" class="text-base text-white">
                        Job Title (optional)
                    </Label>
                    <Input
                        id="jobTitle"
                        v-model="form.jobTitle"
                        placeholder="e.g. Senior Frontend Developer"
                        class="border-2 border-[#e900ff] bg-transparent text-white placeholder:text-gray-500"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.jobTitle" />
                </div>

                <!-- Job Ad Text -->
                <div class="space-y-3">
                    <Label for="jobAdText" class="text-base text-white">
                        Paste Job Ad
                    </Label>
                    <Textarea
                        id="jobAdText"
                        v-model="form.jobAdText"
                        placeholder="Paste the full job posting including requirements and qualifications..."
                        rows="12"
                        class="border-2 border-dashed border-[#e900ff] bg-transparent font-mono text-sm text-white placeholder:text-gray-500"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.jobAdText" />
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="clearForm"
                        class="border-2 border-[#e900ff] bg-transparent text-[#e900ff] hover:bg-[#e900ff]/10"
                        :disabled="form.processing"
                    >
                        Clear
                    </Button>
                    <Button
                        type="submit"
                        class="gap-2 bg-[#e900ff] text-white hover:bg-[#d100e6]"
                        :disabled="form.processing || !form.jobAdText"
                    >
                        {{
                            form.processing ? 'Analyzing...' : 'Analyze Job Ad'
                        }}
                        <ArrowRight class="h-4 w-4" />
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
