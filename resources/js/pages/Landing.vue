<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { index } from '@/routes/job';
import { store } from '@/routes/login';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CheckCircle,
    FileText,
    Glasses,
    Target,
} from 'lucide-vue-next';
</script>

<template>
    <Head title="Fittable - Find the perfect job match" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Header -->
        <header class="border-b bg-white dark:border-gray-800">
            <div
                class="container mx-auto flex items-center justify-between px-6 py-4"
            >
                <div class="text-xl font-bold text-gray-900">Fittable</div>
                <nav class="flex items-center gap-6">
                    <Link
                        :href="store.url()"
                        class="text-md font-semibold text-primary hover:text-gray-900 dark:hover:text-white"
                        >Login</Link
                    >
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative bg-slate-700 py-20 md:py-32">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-10 dark:opacity-20"
                style="
                    background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBJrz3dY6GaWlnFuEBRBO7l-K-qwxpReOis102-sLTnbD1XiSAyw81d74_6LiWxXWWzqe-QsGEXh0Pku5n16rQtkpCtEXTE-Ec6WCD8qqrRVmrRaSoqF1CpCV1Tv_q2y0jNx9DMd3mp_4ch7nJ2PrtTlUOCX9arMMB8um3JDaCTIsqZGu94mdN9IXJnYHvXNE0uUBDSW-5Z7Ctgh_cxRnhUhN470PraZCe0OsAnMo_ScaUUDjgcCW7gPkq_o_nf543bPFYd2s2fvL-o');
                "
            ></div>
            <div
                class="from-background-light via-background-light/50 to-background-light dark:from-background-dark dark:via-background-dark/50 dark:to-background-dark absolute inset-0 bg-gradient-to-b"
            ></div>
            <div class="relative z-10 container mx-auto px-4">
                <div class="mx-auto max-w-3xl text-center">
                    <h1
                        class="text-4xl font-black tracking-tighter text-gray-900 text-white sm:text-5xl md:text-6xl"
                    >
                        Unlock Your Career Potential with
                        <span class="text-primary">Fittable</span>
                    </h1>
                    <p class="mt-6 text-xl font-medium text-gray-300">
                        Fittable simplifies job and talent search by focusing on
                        the qualifications that matter most.
                    </p>
                    <div class="mt-10 flex flex-wrap justify-center gap-4">
                        <Link :href="index.url()">
                            <Button
                                size="lg"
                                variant="primary"
                                class="flex h-12 min-w-[160px] items-center justify-center rounded-lg bg-primary px-6 text-base font-bold text-white shadow-lg shadow-primary/30 transition-transform duration-200 hover:scale-105"
                            >
                                Job Seeker
                            </Button>
                        </Link>
                        <button
                            class="flex h-12 min-w-[160px] items-center justify-center rounded-lg bg-primary/20 px-6 text-base font-bold text-primary backdrop-blur-sm transition-colors hover:bg-primary/30"
                        >
                            <span>Recruiter</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="bg-white py-20">
            <div class="container mx-auto px-6">
                <h2 class="mb-12 text-center text-5xl font-bold text-gray-900">
                    How it works
                </h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Step 1 -->
                    <div class="border px-6 py-6 text-center">
                        <div
                            class="mx-auto mb-6 flex items-center justify-center rounded-xl"
                        >
                            <Glasses class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">
                            Analyze Job Descriptions
                        </h3>
                        <p class="text-gray-600">
                            Paste a job description to identify the core skills
                            and qualifications required.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="border px-6 py-6 text-center">
                        <div
                            class="mx-auto mb-6 flex items-center justify-center rounded-xl"
                        >
                            <FileText class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">
                            Match Resume
                        </h3>
                        <p class="text-gray-600">
                            Fittable compares the resume against the job
                            description, highlighting areas of strength and
                            potential gaps.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="border px-6 py-6 text-center">
                        <div
                            class="mx-auto mb-6 flex items-center justify-center rounded-xl"
                        >
                            <Target class="h-8 w-8 text-primary" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900">
                            Find The Ideal Role
                        </h3>
                        <p class="text-gray-600">
                            Discover roles that align with your skills and
                            career goals, maximizing your chances of success.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Visual Skill Matching -->
        <section class="container mx-auto px-6 py-20">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <h2 class="mb-6 text-4xl font-bold text-gray-900">
                        Visual skill matching
                    </h2>
                    <p class="mb-6 text-gray-600">
                        See exactly how your skills align with job requirements
                        through clear visual representations. Identify your
                        strengths and areas for growth at a glance.
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
                <div
                    class="rounded-3xl bg-gradient-to-br from-slate-500 to-slate-700 p-12"
                >
                    <div
                        class="relative rounded-xl border border-gray-100 bg-white p-8 shadow-sm"
                    >
                        <div class="mb-8 flex items-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    class="text-blue-600"
                                >
                                    <path
                                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                    ></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div
                                    class="mb-2.5 h-2.5 w-24 rounded-full bg-gray-200"
                                ></div>
                                <div
                                    class="h-2 w-16 rounded-full bg-gray-100"
                                ></div>
                            </div>
                        </div>

                        <div class="mb-8 space-y-3">
                            <div>
                                <div
                                    class="mb-1 flex items-center justify-between"
                                >
                                    <div
                                        class="h-2 w-20 rounded-full bg-gray-200"
                                    ></div>
                                    <div
                                        class="text-xs font-medium text-green-600"
                                    >
                                        92%
                                    </div>
                                </div>
                                <div
                                    class="h-1.5 w-full rounded-full bg-gray-100"
                                >
                                    <div
                                        class="h-1.5 rounded-full bg-green-500"
                                        style="width: 92%"
                                    ></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    class="mb-1 flex items-center justify-between"
                                >
                                    <div
                                        class="h-2 w-16 rounded-full bg-gray-200"
                                    ></div>
                                    <div
                                        class="text-xs font-medium text-blue-600"
                                    >
                                        85%
                                    </div>
                                </div>
                                <div
                                    class="h-1.5 w-full rounded-full bg-gray-100"
                                >
                                    <div
                                        class="h-1.5 rounded-full bg-blue-500"
                                        style="width: 85%"
                                    ></div>
                                </div>
                            </div>
                            <div>
                                <div
                                    class="mb-1 flex items-center justify-between"
                                >
                                    <div
                                        class="h-2 w-24 rounded-full bg-gray-200"
                                    ></div>
                                    <div
                                        class="text-xs font-medium text-indigo-600"
                                    >
                                        78%
                                    </div>
                                </div>
                                <div
                                    class="h-1.5 w-full rounded-full bg-gray-100"
                                >
                                    <div
                                        class="h-1.5 rounded-full bg-indigo-500"
                                        style="width: 78%"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div
                                class="h-2.5 w-20 rounded-full bg-gray-200"
                            ></div>
                            <div
                                class="rounded-full border border-pink-200 bg-primary px-2.5 py-1 text-xs font-medium text-white"
                            >
                                Perfect Fit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Employers -->
        <section class="bg-white py-20">
            <div class="container mx-auto px-6">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <!-- Candidate List Visual -->
                    <div
                        class="space-y-4 rounded-3xl bg-gradient-to-br from-slate-500 to-slate-700 p-12"
                    >
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div
                                    class="mb-1 h-2 w-32 rounded bg-gray-200"
                                ></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span
                                class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                                >92% Match</span
                            >
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div
                                    class="mb-1 h-2 w-32 rounded bg-gray-200"
                                ></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span
                                class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700"
                                >87% Match</span
                            >
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-gray-600"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                            >
                                <span class="text-xs">🔍</span>
                            </div>
                            <div class="flex-1">
                                <div
                                    class="mb-1 h-2 w-32 rounded bg-gray-200"
                                ></div>
                                <div class="h-2 w-48 rounded bg-gray-100"></div>
                            </div>
                            <span
                                class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700"
                                >76% Match</span
                            >
                        </div>
                    </div>

                    <div class="">
                        <h2 class="mb-6 text-4xl font-bold text-gray-900">
                            For recruiters
                        </h2>
                        <p class="mb-6 text-gray-600">
                            Find the perfect candidates with our intelligent
                            resume filtering tool. Quickly identify candidates
                            whose skills align with your job requirements.
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
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-slate-600 to-slate-800 py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="mb-4 text-3xl font-bold text-white">
                    Start your journey with Fittable towards the ideal career
                    path!
                </h2>
                <p class="mb-8 text-blue-100">
                    Whether you're looking for a job or hiring talent, our
                    platform connects the right people with the right
                    opportunities.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <Link :href="index.url()">
                        <Button
                            size="lg"
                            variant="primary"
                            class="gap-2 bg-primary text-white hover:bg-primary/90"
                        >
                            Find Jobs
                            <ArrowRight class="h-4 w-4" />
                        </Button>
                    </Link>
                    <Button
                        size="lg"
                        variant="outline"
                        class="bg-primary/20 text-white hover:bg-primary/30"
                    >
                        For Recruiters
                    </Button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t bg-white py-8">
            <div class="container mx-auto px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        <strong class="font-bold text-gray-900"
                            >Fittable</strong
                        >
                        <span class="ml-2">© 2025 All rights reserved</span>
                    </div>
                    <nav class="flex flex-wrap gap-6">
                        <Link
                            href="#"
                            class="text-sm text-gray-600 hover:text-gray-900"
                            >Home</Link
                        >
                        <Link
                            href="#"
                            class="text-sm text-gray-600 hover:text-gray-900"
                            >Jobs</Link
                        >
                        <Link
                            href="#"
                            class="text-sm text-gray-600 hover:text-gray-900"
                            >Match</Link
                        >
                        <Link
                            href="#"
                            class="text-sm text-gray-600 hover:text-gray-900"
                            >Employers</Link
                        >
                        <Link
                            href="#"
                            class="text-sm text-gray-600 hover:text-gray-900"
                            >Privacy</Link
                        >
                    </nav>
                </div>
            </div>
        </footer>
    </div>
</template>
