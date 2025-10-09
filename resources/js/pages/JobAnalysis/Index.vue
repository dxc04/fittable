<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { analyze } from '@/routes/job';
import { Head, useForm } from '@inertiajs/vue3';

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

const sampleJobAd = `Senior Full-Stack Developer

About Us:
TechVision Inc. is a fast-growing SaaS company revolutionizing project management.

Job Description:
We're seeking an experienced Full-Stack Developer to join our engineering team.

Requirements:
- 5+ years of experience with JavaScript/TypeScript
- Strong proficiency in React, Vue.js, or Angular
- Experience with Node.js and Express
- Database expertise (PostgreSQL, MongoDB)
- RESTful API design and implementation
- Git version control
- Bachelor's degree in Computer Science (preferred)

Nice to Have:
- Experience with AWS or Azure
- GraphQL knowledge
- Docker/Kubernetes experience
- CI/CD pipeline experience

Responsibilities:
- Develop and maintain web applications
- Collaborate with cross-functional teams
- Write clean, maintainable code
- Participate in code reviews
- Mentor junior developers

Benefits:
- Competitive salary ($120,000 - $160,000)
- Health, dental, and vision insurance
- 401(k) matching
- Flexible work hours
- Remote work options
- Professional development budget`;

const useSample = () => {
    form.jobAdText = sampleJobAd;
};
</script>

<template>
    <Head title="Job Fit Analysis" />

    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
    >
        <div class="container mx-auto px-4 py-12">
            <!-- Header -->
            <div class="mb-12 text-center">
                <h1
                    class="mb-4 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl dark:text-white"
                >
                    Job Fit Analysis
                </h1>
                <p
                    class="mx-auto max-w-2xl text-lg text-gray-600 dark:text-gray-300"
                >
                    Paste a job advertisement below and let AI analyze the
                    skills, requirements, and responsibilities with beautiful
                    visual insights.
                </p>
            </div>

            <!-- Main Card -->
            <Card class="mx-auto max-w-4xl shadow-xl">
                <CardHeader>
                    <CardTitle>Paste Job Advertisement</CardTitle>
                    <CardDescription>
                        Enter the complete job posting text for AI-powered
                        analysis
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Optional Fields -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="jobTitle"
                                    >Job Title
                                    <span class="text-xs text-muted-foreground"
                                        >(Optional)</span
                                    ></Label
                                >
                                <Input
                                    id="jobTitle"
                                    v-model="form.jobTitle"
                                    placeholder="e.g., Senior Full-Stack Developer"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.jobTitle" />
                            </div>
                            <div class="space-y-2">
                                <Label for="company"
                                    >Company
                                    <span class="text-xs text-muted-foreground"
                                        >(Optional)</span
                                    ></Label
                                >
                                <Input
                                    id="company"
                                    v-model="form.company"
                                    placeholder="e.g., TechVision Inc."
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.company" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <Label for="jobAdText"
                                    >Job Advertisement Text</Label
                                >
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    @click="useSample"
                                    class="text-xs"
                                >
                                    Use Sample
                                </Button>
                            </div>
                            <Textarea
                                id="jobAdText"
                                v-model="form.jobAdText"
                                placeholder="Paste the job advertisement here..."
                                rows="16"
                                class="font-mono text-sm"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.jobAdText" />
                            <p class="text-xs text-muted-foreground">
                                Minimum 50 characters, maximum 10,000 characters
                            </p>
                        </div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                :disabled="form.processing || !form.jobAdText"
                                class="flex-1"
                            >
                                {{
                                    form.processing
                                        ? 'Analyzing...'
                                        : 'Analyze Job Advertisement'
                                }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Features -->
            <div class="mx-auto mt-16 max-w-4xl">
                <h2
                    class="mb-8 text-center text-2xl font-semibold text-gray-900 dark:text-white"
                >
                    What You'll Get
                </h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg"
                                >Skills Radar Chart</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Visual representation of required skills with
                                proficiency levels
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg"
                                >Responsibilities Breakdown</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Bar chart showing importance of key
                                responsibilities
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg"
                                >Requirements Timeline</CardTitle
                            >
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                Prioritized view of must-have vs nice-to-have
                                requirements
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
