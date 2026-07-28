<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-50 dark:bg-zinc-950">
    @php
        $user = auth()->user();

        $roleLabel = match ($user->role) {
            \App\Models\User::ROLE_ADMIN => 'Administrator',
            \App\Models\User::ROLE_TEACHER => 'Guru',
            \App\Models\User::ROLE_STUDENT => 'Peserta Didik',
            default => 'Pengguna',
        };

        $roleDescription = match ($user->role) {
            \App\Models\User::ROLE_ADMIN => 'Pusat kontrol Questify',
            \App\Models\User::ROLE_TEACHER => 'Kelola pembelajaran',
            \App\Models\User::ROLE_STUDENT => 'Mulai perjalanan belajar',
            default => 'Platform Questify',
        };
    @endphp

    <flux:sidebar
        sticky
        collapsible="mobile"
        class="border-e border-blue-100 bg-white/95 shadow-sm backdrop-blur-xl dark:border-blue-950/60 dark:bg-zinc-950/95"
    >
        <flux:sidebar.header>
            <x-app-logo
                :sidebar="true"
                href="{{ route('dashboard') }}"
                wire:navigate
            />

            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        {{-- Identitas role --}}
        <div class="mx-2 mb-5 overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-violet-50 p-3.5 dark:border-blue-900/60 dark:from-blue-950/50 dark:to-violet-950/30">
            <div class="flex items-center gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-900 text-sm font-bold text-white shadow-lg shadow-blue-500/20">
                    {{ $user->initials() }}
                </div>

                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-neutral-900 dark:text-white">
                        {{ $roleLabel }}
                    </p>

                    <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">
                        {{ $roleDescription }}
                    </p>
                </div>
            </div>
        </div>

        <flux:sidebar.nav>
            @if ($user->isAdmin())
                <flux:sidebar.group
                    :heading="__('Menu Admin')"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="home"
                        :href="route('admin.dashboard')"
                        :current="request()->routeIs('admin.dashboard')"
                        wire:navigate
                    >
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="users"
                        :href="route('admin.users.index')"
                        :current="request()->routeIs('admin.users.*')"
                        wire:navigate
                    >
                        {{ __('Kelola Pengguna') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    :heading="__('Segera Hadir')"
                    class="mt-4 grid"
                >
                    <flux:sidebar.item
                        icon="book-open-text"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Kelola Modul') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="chart-bar"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Monitoring Aktivitas') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

            @elseif ($user->isTeacher())
                <flux:sidebar.group
                    :heading="__('Menu Guru')"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="home"
                        :href="route('teacher.dashboard')"
                        :current="request()->routeIs('teacher.dashboard')"
                        wire:navigate
                    >
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    :heading="__('Segera Hadir')"
                    class="mt-4 grid"
                >
                    <flux:sidebar.item
                        icon="book-open-text"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Materi dan Misi') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="users"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Monitoring Peserta Didik') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="chart-bar"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Hasil Pembelajaran') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

            @elseif ($user->isStudent())
                <flux:sidebar.group
                    :heading="__('Menu Peserta Didik')"
                    class="grid"
                >
                    <flux:sidebar.item
                        icon="home"
                        :href="route('student.dashboard')"
                        :current="request()->routeIs('student.dashboard')"
                        wire:navigate
                    >
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group
                    :heading="__('Segera Hadir')"
                    class="mt-4 grid"
                >
                    <flux:sidebar.item
                        icon="book-open-text"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Misi Pembelajaran') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="chart-bar"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Progress dan XP') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item
                        icon="trophy"
                        href="#"
                        class="pointer-events-none opacity-50"
                    >
                        {{ __('Badge') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
