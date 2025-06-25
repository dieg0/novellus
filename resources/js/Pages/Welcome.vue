<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { Icon } from "@iconify/vue";
import LoanForm from "./Loan/LoanForm.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div
        class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 bg-[url(/imgs/Novellus-BG-1.jpg)] bg-cover bg-center"
    >
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" />
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:justify-center lg:col-start-2"></div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main
                    class="flex flex-col items-center justify-center gap-10 h-lvh"
                >
                    <img src="/imgs/Novellus-logo.png" class="w-1/3" />
                    <label for="my_modal_7" class="btn btn-xl">
                        Calculate Your Loan
                        <Icon
                            icon="line-md:briefcase-check"
                            class="w-8 h-auto"
                        />
                    </label>
                    <input
                        type="checkbox"
                        id="my_modal_7"
                        class="modal-toggle"
                    />
                    <div class="modal" role="dialog">
                        <div class="modal-box w-11/12 max-w-5xl">
                            <h3 class="text-lg font-bold text-left">
                                Calculate Your Loan
                            </h3>
                            <LoanForm />
                        </div>
                        <label class="modal-backdrop" for="my_modal_7"
                            >Close</label
                        >
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
