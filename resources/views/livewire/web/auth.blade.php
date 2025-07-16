<div class="w-lg bg-gray-800 p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold text-yellow-400 mb-6 text-center">
        {{ $mode === 'register' ? 'Crie sua conta' : 'Faça login' }}</h2>

    <form wire:submit.prevent="{{ $mode === 'register' ? 'register' : 'login' }}" class="space-y-5">
        @if (session()->has('message'))
            <div class="bg-green-500 text-white p-3 rounded-lg text-center">
                {{ session('message') }}
            </div>
        @endif

        @if ($mode === 'register')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300">Nome completo</label>
                <input type="text" id="name" name="name" required wire:model.live="form.name"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                @error('form.name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif
        <div>
            @if ($mode === 'register')
                <label for="email" class="block text-sm font-medium text-gray-300">E-mail</label>
                <input type="email" id="email" name="email" required wire:model.live="form.email"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 " />
                @error('form.email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            @else
                <label for="email" class="block text-sm font-medium text-gray-300">E-mail</label>
                <input type="email" id="email" name="email" required wire:model.live="login_email"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 " />
                @error('login_email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            @endif
        </div>
        @if ($mode === 'register')
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Senha</label>
                <input type="password" id="password" name="password" required wire:model.live="form.password"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                @error('form.password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirmar
                    senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required required
                    wire:model.defer="form.password_confirmation"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                @error('form.password_confirmation')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        @else
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Senha</label>
                <input type="password" id="password" name="password" required wire:model.live="login_password"
                    class="w-full mt-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400" />
                @error('login_password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif
        <div>
            @if ($mode == 'register')
                <button type="submit"
                    class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-bold rounded-lg transition duration-200"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register">Criar conta</span>
                    <span wire:loading wire:target="register">Registrando...</span>
                </button>
            @else
                <button type="submit"
                    class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-bold rounded-lg transition duration-200"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="login">Logar</span>
                    <span wire:loading wire:target="login">Logando...</span>
                </button>
            @endif
        </div>

        <p class="text-sm text-gray-400 text-center mt-4">
            {{ $mode === 'register' ? 'Já' : 'Não' }} Já tem uma conta?
            <a class="text-yellow-400 hover:underline"
                wire:click.prevent="$set('mode', '{{ $mode === 'register' ? 'login' : 'register' }}')">Entrar</a>
        </p>
    </form>
</div>
