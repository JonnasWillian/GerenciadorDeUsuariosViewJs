<script setup>
import { ref, onMounted, computed, defineProps } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { vMaska } from 'maska/vue';
import { PlusCircle, FileText, Paperclip, Trash2, Edit, Save, X, Download } from 'lucide-vue-next';

const user = computed(() => usePage().props.auth.user);
const props = defineProps({
    cliente: Object
});

const usuario = ref(null);
const anotacoes = ref([]);
const arquivos = ref([]);
const activeTab = ref('info');
const editingNoteId = ref(null);
const showAddNote = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);

// Formulário para anotações
const noteForm = useForm({
    conteudo: '',
    usuario_id: null,
    user_id: user.value.id,
});

const buscarUsuario = async (id) => {
    try {
        const response = await axios.get(`/api/usuarioPerfil/${id}`);
        usuario.value = response.data[0];
    } catch (error) {
        console.error("Erro ao buscar usuario:", error);
    }
};

onMounted(() => {
    const idPerfil = sessionStorage.getItem('idPerfil');
    if (idPerfil) {
        buscarUsuario(idPerfil);
    } else {
        router.visit(route('dashboard'));
    }
});
</script>

<template>
    <Head title="Perfil do Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Perfil do Usuario
                </h2>
                <Link :href="route('dashboard')" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300 transition">
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="usuario" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800">{{ usuario.nome }}</h1>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <Link :href="`/editar-usuario/${usuario.id}`" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                    <!-- Função de editar dados do usuario -->
                                    <Edit class="w-4 h-4 mr-2" />
                                    Editar Usuario
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button 
                                @click="activeTab = 'info'" 
                                :class="[
                                    'py-4 px-6 font-medium text-sm border-b-2 focus:outline-none',
                                    activeTab === 'info' 
                                        ? 'border-blue-500 text-blue-600' 
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Informações
                            </button>
                            <button 
                                @click="activeTab = 'anotacoes'" 
                                :class="[
                                    'py-4 px-6 font-medium text-sm border-b-2 focus:outline-none',
                                    activeTab === 'anotacoes' 
                                        ? 'border-blue-500 text-blue-600' 
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Anotações
                            </button>
                            <button 
                                @click="activeTab = 'arquivos'" 
                                :class="[
                                    'py-4 px-6 font-medium text-sm border-b-2 focus:outline-none',
                                    activeTab === 'arquivos' 
                                        ? 'border-blue-500 text-blue-600' 
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                Arquivos
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <div v-if="activeTab === 'info'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Informações de Contato</h3>
                                        <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500">Email</p>
                                                    <input class="text-sm font-medium" v-model="usuario.email" type="text">
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Telefone</p>
                                                    <input v-model="usuario.telefone" class="text-sm font-medium" v-maska data-maska="(##) #####-####"/>
                                                </div>
                                                <div v-if="usuario.descricao">
                                                    <p class="text-xs text-gray-500">Descrição</p>
                                                    <input class="text-sm font-medium" v-model="usuario.descricao" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Documentos</h3>
                                        <div class="mt-2 p-4 bg-gray-50 rounded-md">
                                            <div class="grid grid-cols-1 gap-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="activeTab === 'anotacoes'" class="space-y-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Anotações</h3>
                                <button 
                                    v-if="!showAddNote"
                                    @click="showAddNote = true" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <PlusCircle class="w-4 h-4 mr-2" />
                                    Nova Anotação
                                </button>
                            </div>

                            <!-- Formulário para adicionar anotação -->
                            <div v-if="showAddNote" class="bg-gray-50 p-4 rounded-md">
                                <textarea 
                                    v-model="noteForm.conteudo" 
                                    rows="4" 
                                    class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Digite sua anotação aqui..."
                                ></textarea>
                                <div class="flex justify-end mt-3 space-x-2">
                                    <button 
                                        @click="showAddNote = false" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <X class="w-4 h-4 mr-2" />
                                        Cancelar
                                    </button>
                                    <button 
                                        @click="adicionarAnotacao" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        :disabled="!noteForm.conteudo"
                                    >
                                        <Save class="w-4 h-4 mr-2" />
                                        Salvar
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de anotações -->
                            <div v-if="anotacoes.length > 0" class="space-y-4">
                                <div v-for="anotacao in anotacoes" :key="anotacao.id" class="bg-white border rounded-md p-4 shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <FileText class="w-4 h-4 mr-2" />
                                            <span>{{ formatarData(anotacao.created_at) }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button 
                                                @click="editarAnotacao(anotacao)" 
                                                class="text-gray-500 hover:text-blue-600"
                                                v-if="editingNoteId !== anotacao.id"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </button>
                                            <button 
                                                @click="excluirAnotacao(anotacao.id)" 
                                                class="text-gray-500 hover:text-red-600"
                                                v-if="editingNoteId !== anotacao.id"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Visualização da anotação -->
                                    <div v-if="editingNoteId !== anotacao.id" class="mt-2">
                                        <p class="text-gray-700 whitespace-pre-line">{{ anotacao.conteudo }}</p>
                                    </div>
                                    
                                    <!-- Edição da anotação -->
                                    <div v-else class="mt-2">
                                        <textarea 
                                            v-model="editNoteForm.conteudo" 
                                            rows="4" 
                                            class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        ></textarea>
                                        <div class="flex justify-end mt-3 space-x-2">
                                            <button 
                                                @click="cancelarEdicaoAnotacao" 
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                <X class="w-4 h-4 mr-2" />
                                                Cancelar
                                            </button>
                                            <button 
                                                @click="salvarEdicaoAnotacao" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                <Save class="w-4 h-4 mr-2" />
                                                Salvar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mensagem quando não há anotações -->
                            <div v-else-if="!showAddNote" class="text-center py-8">
                                <FileText class="w-12 h-12 mx-auto text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma anotação</h3>
                                <p class="mt-1 text-sm text-gray-500">Comece adicionando uma nova anotação para este usuario.</p>
                            </div>
                        </div>

                        <!-- Aba de Arquivos -->
                        <div v-if="activeTab === 'arquivos'" class="space-y-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Arquivos</h3>
                                <label class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                                    <Paperclip class="w-4 h-4 mr-2" />
                                    Anexar Arquivo
                                    <input type="file" class="hidden" @change="uploadArquivo" />
                                </label>
                            </div>

                            <!-- Progresso de upload -->
                            <div v-if="isUploading" class="bg-gray-50 p-4 rounded-md">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: uploadProgress + '%' }"></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">{{ uploadProgress }}%</span>
                                </div>
                            </div>

                            <!-- Lista de arquivos -->
                            <div v-if="arquivos.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamanho</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="arquivo in arquivos" :key="arquivo.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <Paperclip class="flex-shrink-0 h-4 w-4 text-gray-400 mr-2" />
                                                    <div class="text-sm font-medium text-gray-900">{{ arquivo.nome }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ formatarTamanho(arquivo.tamanho || 0) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ formatarData(arquivo.created_at) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a :href="arquivo.url" download class="text-blue-600 hover:text-blue-900">
                                                        <Download class="w-4 h-4" />
                                                    </a>
                                                    <button @click="excluirArquivo(arquivo.id)" class="text-red-600 hover:text-red-900">
                                                        <Trash2 class="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div v-else class="text-center py-8">
                                <Paperclip class="w-12 h-12 mx-auto text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum arquivo</h3>
                                <p class="mt-1 text-sm text-gray-500">Comece anexando arquivos para este usuario.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-center items-center h-64">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.3s;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }
</style>