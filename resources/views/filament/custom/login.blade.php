<x-filament-panels::page.simple>
    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Background Effects -->
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-gray-950 dark:to-indigo-950">
            <div class="absolute inset-0 bg-[url('/pattern.svg')] opacity-[0.2] dark:opacity-[0.05]"></div>
            <div class="absolute top-10 left-10 w-40 h-40 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-20 w-36 h-36 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-24 right-40 w-52 h-52 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
            <div class="absolute bottom-0 left-20 w-44 h-44 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-6000"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="w-20 h-20 rounded-xl bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg flex items-center justify-center shadow-lg">
                    @if (filled($logo = filament()->getLogoUrl()))
                        <img src="{{ $logo }}" alt="{{ filament()->getBrandName() }}" class="h-10">
                    @else
                        <span class="font-bold text-xl text-primary-500">{{ filament()->getBrandName() }}</span>
                    @endif
                </div>
            </div>

            <div class="bg-white/75 dark:bg-gray-900/75 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                <div class="px-6 py-8">
                    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">
                        {{ __('filament-panels::pages/auth/login.title') }}
                    </h2>

                    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

                    <x-filament-panels::form wire:submit="authenticate">
                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.fields.before') }}

                        @if (count($panels = filament()->getPanels()) > 1)
                            <x-filament::fieldset>
                                <x-filament::fieldset.label>
                                    {{ __('filament-panels::pages/auth/login.panels.fieldset.label') }}
                                </x-filament::fieldset.label>

                                <div class="grid grid-cols-2 gap-x-6">
                                    @foreach ($panels as $panel)
                                        <label for="panel_{{ $panel->getId() }}" class="cursor-pointer">
                                            <x-filament::input.radio
                                                id="panel_{{ $panel->getId() }}"
                                                name="panel"
                                                value="{{ $panel->getId() }}"
                                                wire:model.live="panel"
                                            />

                                            <x-filament::input.radio.label>
                                                {{ $panel->getName() }}
                                            </x-filament::input.radio.label>
                                        </label>
                                    @endforeach
                                </div>
                            </x-filament::fieldset>
                        @endif

                        {{ $this->form }}

                        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.fields.after') }}

                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </x-filament-panels::form>

                    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
                </div>

                @if (filament()->hasRegistration() || filament()->hasPasswordReset())
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-200/50 dark:border-gray-700/50 space-y-3">
                        @if (filament()->hasRegistration())
                            <div class="text-center">
                                <x-filament::link
                                    :href="filament()->getRegistrationUrl()"
                                    class="text-sm"
                                >
                                    {{ __('filament-panels::pages/auth/login.actions.register.label') }}
                                </x-filament::link>
                            </div>
                        @endif

                        @if (filament()->hasPasswordReset())
                            <div class="text-center">
                                <x-filament::link
                                    :href="filament()->getRequestPasswordResetUrl()"
                                    class="text-sm"
                                >
                                    {{ __('filament-panels::pages/auth/login.actions.request_password_reset.label') }}
                                </x-filament::link>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}
                </p>
            </div>
        </div>
    </div>

    <style>
        .animate-blob {
            animation: blob 10s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        .animation-delay-6000 {
            animation-delay: 6s;
        }
        
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -30px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
    </style>
</x-filament-panels::page.simple>