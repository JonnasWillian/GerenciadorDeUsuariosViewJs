<script setup>
    import { ref, onMounted, computed, defineProps } from 'vue';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
    import axios from 'axios';
    import { vMaska } from 'maska/vue';
    import { PlusCircle, FileText, Paperclip, Trash2, Edit, Save, X, Download, Upload} from 'lucide-vue-next';
    import Swal from 'sweetalert2';

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
    const idPerfil = sessionStorage.getItem('idPerfil');
    const arquivosPendentes = ref([]);

    const noteForm = useForm({
        descricao: '',
        usuario_id: user.value.id,
    });

    const editNoteForm = useForm({
        id: '',
        descricao: '',
        usuario_id: user.value.id,
    });

    const messageError = async (messagem) => {
        await Swal.fire({
            title: 'Erro!',
            text: messagem,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    }

    // Usuário
    const buscarUsuario = async (id) => {
        try {
            const response = await axios.get(`/api/usuarioPerfil/${id}`);
            usuario.value = response.data[0];

        } catch (error) {
            messageError("Erro ao buscar usuário!")
            console.error("Erro ao buscar usuario:", error);
        }
    };

    const editarUsuario = async () => {
        usuario.value.telefone = usuario.value.telefone.replace(/\D/g, '');

        try {
            const response = await axios.put(`api/usuarios/${usuario.value.id}`, {
                ...usuario.value
            })

            await Swal.fire({
                title: 'Sucesso!',
                text: 'Usuário atualizado com sucesso!',
                icon: 'success',
                confirmButtonText: 'Ok'
            });

            router.visit(route('perfilUsuario'))
        } catch (error) {
            messageError("Erro ao atualizar usuário!")
            console.log('error', error)
        }
    }

    // Anotação
    const buscarAnotacao = async (id) => {
        try {
            const response = await axios.get(`/api/anotacao/${id}`);
            anotacoes.value = response.data;

        } catch (error) {
            messageError("Erro ao buscar anotação usuário!")
        }
    };

    const cadastrarAnotacao = async () => {
        try {
            const response = await axios.post(`api/anotacao`, {
                ...noteForm
            })

            await Swal.fire({
                title: 'Sucesso!',
                text: 'Anotação criada com sucesso!',
                icon: 'success',
                confirmButtonText: 'Ok'
            });

            router.visit(route('perfilUsuario'))
        } catch (error) {
            messageError("Erro ao atualizar usuário!")
            console.log('error', error)
        }
    }

    const editarAnotacao = (anotacao) => {
        editNoteForm.id = anotacao.id;;
        editNoteForm.descricao = anotacao.descricao
        editingNoteId.value = anotacao.id;
    }

    const cancelarEdicaoAnotacao = () => {
        editingNoteId.value = null;
    }

    const salvarEdicaoAnotacao = async () => {
        try {
            await axios.put(`api/anotacao/${editNoteForm.id}`,editNoteForm);
            buscarAnotacao(idPerfil)
            editingNoteId.value = null;
        } catch (error) {
            console.log('error', error)
            messageError("Erro ao editar anotação!")
        }
    }

    const excluirAnotacao = async (id) => {
        try {
            const result = await Swal.fire({
                title: 'Confirmação de exclusão',
                text: 'Você deseja, realmente apagar esta anotação?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Não',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`api/anotacao/${id}`);
                    buscarAnotacao(idPerfil)

                    await Swal.fire({
                        title: 'Sucesso!',
                        text: 'Anotação deletada com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });
                } catch (error) {
                    console.log(error)
                    messageError("Erro ao apagar anotação!")
                }
            }
        } catch (error) {
            console.log('error', error)
        }
    }

    // arquivos
    const buscarAnexo = async (id) => {
        try {
            const response = await axios.post(`/api/buscarArquivo/`, {user_id: id});
            arquivos.value = response.data;

        } catch (error) {
            messageError("Erro ao buscar anexo do usuário!")
        }
    };

    const adicionarArquivos = (event) => {
        const files = Array.from(event.target.files);

        files.forEach(file => {
            const jaExiste = arquivosPendentes.value.some( arquivo =>
                arquivo.name === file.name && arquivo.size === file.size
            )

            if (!jaExiste) {
                arquivosPendentes.value.push({
                    id: Date.now() + Math.random,
                    file: file,
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    isPendng: true,
                })
            }
        });

        event.target.value = null;
    }

    const removerArquivoPendente = async (id) => {
        arquivosPendentes.value = arquivosPendentes.value.filter(arquivo => arquivo.id !== id)
    }

    const salvarArquivos = async () => {
        if (arquivosPendentes.value.length === 0) return;

        isUploading.value = true;
        uploadProgress.value = 0;

        try {
            const totalArquivos = arquivosPendentes.value.length;
            let arquivosProcessados = 0;

            for (const arquivoPendente of arquivosPendentes.value) {
                const formData = new FormData();

                formData.append('arquivo', arquivoPendente.file)
                formData.append('nome', arquivoPendente.name)
                formData.append('usuario_id', idPerfil)

                await axios.post('api/arquivos', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                arquivosProcessados++;
                uploadProgress.value = Math.round((arquivosProcessados / totalArquivos) * 100)
            }

            arquivosPendentes.value = []

            buscarAnexo(idPerfil)

            await Swal.fire({
                title: 'Sucesso!',
                text: `${totalArquivos} arquivo(s) enviado(s) com sucesso!`,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } catch (error) {
            
        }
    }

    const formatarTamanho = (bytes) => {
        if (bytes < 1024) return bytes + ' B';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' KB';
        else return (bytes / 1048576).toFixed(2) + ' MB';
    }

    const excluirArquivo = async (id) => {
        try {
            const result = await Swal.fire({
                title: 'Confirmação de exclusão',
                text: 'Você deseja, realmente apagar este arquivo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Não',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`api/arquivos/${id}`);
                    buscarAnexo(idPerfil)

                    await Swal.fire({
                        title: 'Sucesso!',
                        text: 'Anotação deletada com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });
                } catch (error) {
                    console.log(error)
                    messageError("Erro ao apagar anotação!")
                }
            }
        } catch (error) {
            console.log('error', error)
        }
    }

    onMounted(() => {
        if (idPerfil) {
            buscarUsuario(idPerfil);
            buscarAnotacao(idPerfil);
            buscarAnexo(idPerfil);
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
                                <p @click="editarUsuario()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                    <Edit class="w-4 h-4 mr-2" />
                                    Editar Usuario
                                </p>
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
                                                <div>
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
                                    v-model="noteForm.descricao" 
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
                                        @click="cadastrarAnotacao()" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        :disabled="!noteForm.descricao"
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
                                            <span>{{ anotacao.descricao }}</span>
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
                                            v-model="editNoteForm.descricao" 
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
                                    Selecionar Arquivos
                                    <input type="file" multiple class="hidden" @change="adicionarArquivos" />
                                </label>
                            </div>

                            <!-- Seção de Arquivos Pendentes (Staging) -->
                            <div v-if="arquivosPendentes.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-sm font-medium text-yellow-800">
                                        <Upload class="w-4 h-4 inline mr-1" />
                                        Arquivos Pendentes ({{ arquivosPendentes.length }})
                                    </h4>
                                    <div class="flex space-x-2">
                                        <button @click="arquivosPendentes = []" class="text-sm text-gray-500 hover:text-gray-700">
                                            Limpar Todos
                                        </button>
                                        <button 
                                            @click="salvarArquivos" 
                                            :disabled="isUploading"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                                        >
                                            <Save class="w-4 h-4 mr-2" />
                                            {{ isUploading ? 'Salvando...' : 'Salvar Arquivos' }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Lista de Arquivos Pendentes -->
                                <div class="space-y-2">
                                    <div v-for="arquivo in arquivosPendentes" :key="arquivo.id" class="flex items-center justify-between bg-white p-3 rounded border">
                                        <div class="flex items-center space-x-3">
                                            <Paperclip class="w-4 h-4 text-gray-400" />
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ arquivo.name }}</p>
                                                <p class="text-xs text-gray-500">{{ formatarTamanho(arquivo.size) }}</p>
                                            </div>
                                        </div>
                                        <button @click="removerArquivoPendente(arquivo.id)" class="text-red-500 hover:text-red-700">
                                            <X class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Progresso de Upload -->
                                <div v-if="isUploading" class="mt-4">
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-green-600 h-2.5 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ uploadProgress }}%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de Arquivos Salvos -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-3">
                                    <FileText class="w-4 h-4 inline mr-1" />
                                    Arquivos Salvos
                                </h4>
                                
                                <div v-if="arquivos.length > 0" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamanho</th>
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
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <a :href="`/storage/${arquivo.local}`" download class="text-blue-600 hover:text-blue-900" title="Download">
                                                            <Download class="w-4 h-4" />
                                                        </a>
                                                        <button @click="excluirArquivo(arquivo.id)" class="text-red-600 hover:text-red-900" title="Excluir">
                                                            <Trash2 class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mensagem quando não há arquivos salvos -->
                                <div v-else class="text-center py-8 bg-gray-50 rounded-md">
                                    <Paperclip class="w-12 h-12 mx-auto text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum arquivo salvo</h3>
                                    <p class="mt-1 text-sm text-gray-500">Os arquivos aparecerão aqui após serem salvos.</p>
                                </div>
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