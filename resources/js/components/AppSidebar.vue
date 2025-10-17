<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { index as assessmentsIndex } from '@/routes/assessments';
import { index as analyze } from '@/routes/job';
import { index as jobPostingsIndex } from '@/routes/job-postings';
import { index as resumesIndex } from '@/routes/resumes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookMarked, BookOpen, FileText, Glasses } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => page.props.auth.user?.roles || []);
const isJobSeeker = computed(() => userRoles.value.includes('job_seeker'));

const jobSeekerNav: NavItem[] = [
    {
        title: 'Analyze',
        href: analyze(),
        icon: Glasses,
    },
    {
        title: 'Job Postings',
        href: jobPostingsIndex(),
        icon: BookMarked,
    },
    {
        title: 'Resumes',
        href: resumesIndex(),
        icon: FileText,
    },
    {
        title: 'Assessments',
        href: assessmentsIndex(),
        icon: BookOpen,
    },
];

const recruiterNav: NavItem[] = [
    {
        title: 'Job Postings',
        href: jobPostingsIndex(),
        icon: BookMarked,
    },
    {
        title: 'Resumes',
        href: resumesIndex(),
        icon: FileText,
    },
    {
        title: 'Assessments',
        href: assessmentsIndex(),
        icon: BookOpen,
    },
];

const mainNavItems: NavItem[] = isJobSeeker ? jobSeekerNav : recruiterNav;
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link
                            :href="analyze()"
                            class="flex items-center justify-center"
                        >
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="[]" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
