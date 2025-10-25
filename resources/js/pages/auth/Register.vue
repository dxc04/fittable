<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    registrationRole?: string;
    roleFromSession?: boolean;
}

const { registrationRole, roleFromSession } = withDefaults(
    defineProps<Props>(),
    {
        registrationRole: 'job_seeker',
        roleFromSession: false,
    },
);
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="role">I am a</Label>
                    <div class="flex gap-4">
                        <label
                            class="flex flex-1 items-center gap-2 rounded-lg border p-3 transition-colors"
                            :class="[
                                registrationRole === 'job_seeker'
                                    ? 'border-primary bg-primary/10'
                                    : 'border-input',
                                roleFromSession
                                    ? 'pointer-events-none cursor-not-allowed opacity-90'
                                    : 'cursor-pointer hover:border-primary',
                            ]"
                        >
                            <input
                                type="radio"
                                name="role"
                                value="job_seeker"
                                :checked="registrationRole === 'job_seeker'"
                                :tabindex="3"
                                class="h-4 w-4"
                                :class="
                                    roleFromSession ? 'pointer-events-none' : ''
                                "
                            />
                            <span class="text-sm font-medium">Job Seeker</span>
                        </label>
                        <label
                            class="flex flex-1 items-center gap-2 rounded-lg border p-3 transition-colors"
                            :class="[
                                registrationRole === 'recruiter'
                                    ? 'border-primary bg-primary/10'
                                    : 'border-input',
                                roleFromSession
                                    ? 'pointer-events-none cursor-not-allowed opacity-90'
                                    : 'cursor-pointer hover:border-primary',
                            ]"
                        >
                            <input
                                type="radio"
                                name="role"
                                value="recruiter"
                                :checked="registrationRole === 'recruiter'"
                                :tabindex="3"
                                class="h-4 w-4"
                                :class="
                                    roleFromSession ? 'pointer-events-none' : ''
                                "
                            />
                            <span class="text-sm font-medium">Recruiter</span>
                        </label>
                    </div>
                    <p
                        v-if="roleFromSession"
                        class="text-xs text-muted-foreground"
                    >
                        Role is pre-selected based on your activity
                    </p>
                    <InputError :message="errors.role" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="6"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="7"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
