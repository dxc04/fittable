<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { index } from '@/routes/job';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    CheckCircle,
    FileText,
    Target,
} from 'lucide-vue-next';
</script>

<template>
    <Head title="Fittable - Find the perfect job match" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Header -->
        <header class="border-b bg-white">
            <div class="container mx-auto flex items-center justify-between px-6 py-4">
                <div class="text-xl font-bold text-gray-900">Fittable</div>
                <nav class="flex items-center gap-6">
                    <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Jobs</Link>
                    <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Match</Link>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="container mx-auto px-6 py-20">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <h1 class="mb-6 text-5xl font-bold leading-tight text-gray-900">
                        Find the perfect job match for your skills
                    </h1>
                    <p class="mb-8 text-lg text-gray-600">
                        Our intelligent platform aligns your unique skills with the right
                        opportunities, using visual data to show exactly how you fit.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link :href="index.url()">
                            <Button size="lg" class="gap-2">
                                Find Jobs
                                <ArrowRight class="h-4 w-4" />
                            </Button>
                        </Link>
                        <Link :href="index.url()">
                            <Button size="lg" variant="outline">Upload Resume</Button>
                        </Link>
                    </div>
                </div>

                <!-- Visual Match Display -->
                <div class="rounded-3xl bg-white p-8 shadow-xl">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                            <Target class="h-5 w-5 text-blue-600" />
                        </div>
                        <div class="h-24 w-full rounded-lg bg-gray-100"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-3 flex-1 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[92%] rounded-full bg-green-500"></div>
                            </div>
                            <span class="text-sm font-semibold text-green-600">92%</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-3 flex-1 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[85%] rounded-full bg-blue-500"></div>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">85%</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-3 flex-1 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full w-[78%] rounded-full bg-indigo-500"></div>
                            </div>
                            <span class="text-sm font-semibold text-indigo-600">78%</span>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <span
                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                            >Perfect Match</span
                        >
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="bg-white py-20">
            <div class="container mx-auto px-6">
                <h2 class="mb-12 text-center text-3xl font-bold text-gray-900">
                    How it works
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-100"
                        >
                            <Briefcase class="h-8 w-8 text-blue-600" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">Browse Jobs</h3>
                        <p class="text-gray-600">
                            Explore visually rich job listings that clearly show requirements
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="text-center">
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100"
                        >
                            <FileText class="h-8 w-8 text-indigo-600" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">Upload Resume</h3>
                        <p class="text-gray-600">
                            Submit your resume for intelligent skill analysis
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="text-center">
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-green-100"
                        >
                            <Target class="h-8 w-8 text-green-600" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">Get Matched</h3>
                        <p class="text-gray-600">
                            Receive visual feedback on how well you match with opportunities
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visual Skill Matching -->
        <section class="container mx-auto px-6 py-20">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <h2 class="mb-6 text-3xl font-bold text-gray-900">
                        Visual skill matching
                    </h2>
                    <p class="mb-6 text-gray-600">
                        See exactly how your skills align with job requirements through clear
                        visual representations. Identify your strengths and areas for growth at
                        a glance.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-gray-700">
                            <CheckCircle class="h-5 w-5 text-green-600" />
                            <span>Interactive skill charts</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <CheckCircle class="h-5 w-5 text-green-600" />
                            <span>Clear match percentages</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-700">
                            <CheckCircle class="h-5 w-5 text-green-600" />
                            <span>Personalized recommendations</span>
                        </div>
                    </div>
                </div>

                <!-- Radar Chart Visual -->
                <div class="rounded-3xl bg-gradient-to-br from-blue-100 to-indigo-100 p-12">
                    <div
                        class="mx-auto h-64 w-64 rounded-full border-4 border-blue-200 bg-white opacity-50"
                    ></div>
                </div>
            </div>
        </section>

        <!-- For Employers -->
        <section class="bg-white py-20">
            <div class="container mx-auto px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <!-- Candidate List Visual -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div class="mb-1 h-2 w-32 rounded bg-gray-200"></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">92% Match</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div class="mb-1 h-2 w-32 rounded bg-gray-200"></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">87% Match</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div class="mb-1 h-2 w-32 rounded bg-gray-200"></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">76% Match</span>
                        </div>
                    </div>

                    <div>
                        <h2 class="mb-6 text-3xl font-bold text-gray-900">For employers</h2>
                        <p class="mb-6 text-gray-600">
                            Find the perfect candidates with our intelligent resume filtering
                            tool. Quickly identify candidates whose skills align with your job
                            requirements.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-gray-700">
                                <CheckCircle class="h-5 w-5 text-blue-600" />
                                <span>Filter by skills and experience</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <CheckCircle class="h-5 w-5 text-blue-600" />
                                <span>See match scores at a glance</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-700">
                                <CheckCircle class="h-5 w-5 text-blue-600" />
                                <span>Save time in the hiring process</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <Link href="#" class="text-blue-600 hover:text-blue-700">
                                Learn more about employer tools →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-600 to-indigo-600 py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="mb-4 text-3xl font-bold text-white">
                    Ready to find your perfect match?
                </h2>
                <p class="mb-8 text-blue-100">
                    Whether you're looking for a job or hiring talent, our platform connects
                    the right people with the right opportunities.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <Link :href="index.url()">
                        <Button size="lg" variant="secondary" class="gap-2">
                            Find Jobs
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                    <Button size="lg" variant="outline" class="border-white text-white hover:bg-white/10">
                        For Employers
                    </Button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t bg-white py-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        <strong class="font-bold text-gray-900">Fittable</strong>
                        <span class="ml-2">© 2025 All rights reserved</span>
                    </div>
                    <nav class="flex flex-wrap gap-6">
                        <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Home</Link>
                        <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Jobs</Link>
                        <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Match</Link>
                        <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Employers</Link>
                        <Link href="#" class="text-sm text-gray-600 hover:text-gray-900">Privacy</Link>
                    </nav>
                </div>
            </div>
        </footer>
    </div>
</template>
