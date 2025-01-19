<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        components: {
            Link
        }
    };
</script>

<template>
   <div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800">
    <nav class="bg-gay-900/50 backdrop-blur-sm border-b border-gay-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-white">UserFlow<span class="text-indigo-500">.</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <Link :href="route('login')">
                        <button class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</button>
                    </Link>
                    <Link :href="route('register')">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all">Começar Agora</button>
                    </Link>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
        <div class="text-center">
            <h1 class="text-4xl sm:text-6xl font-bold text-white mb-6">
                Gerencie seus usuários com <span class="text-indigo-500">simplicidade</span>
            </h1>
            <p class="text-xl text-gray-400 mb-8 max-w-2xl mx-auto">Uma plataforma moderna e intuitiva para gerenciar seus usuários, permissões e acessos em um único lugar.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl border border-gray-700 hover:border-indigo-500 transition-all">
                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Gestão Simplificada</h3>
                    <p class="text-gray-400">Interface intuitiva para adicionar, remover e gerenciar usuários com poucos cliques.</p>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl border border-gray-700 hover:border-indigo-500 transition-all">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Controle de Acesso</h3>
                <p class="text-gray-400">Sistema avançado de permissões e papéis para garantir a segurança dos seus dados.</p>
                </div>

                <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl border border-gray-700 hover:border-indigo-500 transition-all">
                <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Análises Detalhadas</h3>
                <p class="text-gray-400">Dashboards e relatórios completos sobre o uso e atividade dos usuários.</p>
                </div>
        </div>
    </div>

    <div class="bg-gray-800/30 backdrop-blur-sm border-y border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-white mb-2">+10k</div>
                <div class="text-gray-400">Usuários Gerenciados</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">99.9%</div>
                <div class="text-gray-400">Uptime Garantido</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2">24/7</div>
                <div class="text-gray-400">Suporte Dedicado</div>
            </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="bg-indigo-600 rounded-2xl p-8 md:p-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Pronto para começar?
            </h2>
            <p class="text-lg text-indigo-200 mb-8 max-w-2xl mx-auto">
            Junte-se a milhares de empresas que já confiam em nossa plataforma para gerenciar seus usuários.
            </p>
            <Link :href="route('register')">
                <button class="bg-white text-indigo-600 hover:bg-indigo-50 px-8 py-4 rounded-lg text-lg font-semibold transition-all transform hover:scale-105">
                Criar Conta Gratuita
                </button>
            </Link>
        </div>
    </div>

    <footer class="bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="text-2xl font-bold text-white mb-4">
                    UserFlow<span class="text-indigo-500">.</span>
                    </div>
                    <p class="text-gray-400">
                    Simplificando a gestão de usuários para empresas modernas.
                    </p>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Produto</h4>
                    <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Funcionalidades</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Preços</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Cases</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Empresa</h4>
                    <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Sobre</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Carreiras</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Suporte</h4>
                    <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Documentação</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Status</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Contato</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                © 2025 UserFlow. Todos os direitos reservados.
            </div>
        </div>
    </footer>

   </div> 
</template>

<style scoped></style>