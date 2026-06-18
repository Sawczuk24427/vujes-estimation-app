<template>
    <v-app>
        <LoginProfileSelector
        v-if="selectedUser == null"
        :users="users"
        @login="loginAs"
        />
        
          <template v-else>
          
            <SidebarMenu 
            :user="selectedUser"
            :currentView="currentView"
            @change-view="currentView = $event"
            @logout="logout"
            />
            <v-main class="bg-grey-lighten-4">
              <v-container class="pa-6">
                
                <EstimationsView 
                  v-if="currentView === 'estimations'"
                  :estimations="processedEstimations"
                  :clients="clients"
                  :filtered-projects-for-estimation="filteredProjectsForEstimation"
                  :filtered-projects-for-search="filteredProjectsForSearch"
                  :estimation-form="newEstimation"
                  :filters="filters"
                  :is-editing="isEditingEstimation"
                  :summary-total-price="summaryTotalPrice"
                  @save="saveEstimation"
                  @cancel="cancelEditEstimation"
                  @edit="editEstimation"
                  @delete="deleteEstimation"
                  @open-client-dialog="openClientDialog"
                  @open-project-dialog="openProjectDialog"
                  />

                <ProjectsView 
                  v-if="currentView === 'projects'"
                  :projects="projects"
                  :clients="clients"
                  :project-form="newProject"
                  :is-editing="isEditingProject"
                  @save="saveProject"
                  @cancel="cancelEditProject"
                  @edit="editProject"
                  @delete="deleteProject"
                  @open-client-dialog="openClientDialog"
                />

                <ClientsView 
                  v-if="currentView === 'clients'"
                  :clients="clients"
                  :client-form="newClient"
                  :is-editing="isEditingClient"
                  @save="saveClient"
                  @cancel="cancelEditClient"
                  @edit="editClient"
                  @delete="deleteClient"
                />
        <QuickAddClientModal 
        v-model:is-open="isClientDialogOpen" 
        @save="handleQuickAddClient" 
        />

        <QuickAddProjectModal 
          v-model:is-open="isProjectDialogOpen" 
          @save="handleQuickAddProject" 
        />
        </v-container>
        </v-main>
        </template>
    </v-app>   
</template>
 
<script>
  import api from './services/api';
  import LoginProfileSelector from './components/LoginProfileSelector.vue';
  import SidebarMenu from './components/SidebarMenu.vue';
  import QuickAddClientModal from './components/QuickAddClientModal.vue';
  import QuickAddProjectModal from './components/QuickAddProjectModal.vue';
  import ClientsView from './views/ClientsView.vue';
  import ProjectsView from './views/ProjectsView.vue';
  import EstimationsView from './views/EstimationsView.vue';

    export default{
        components: {
          LoginProfileSelector,
          SidebarMenu,
          QuickAddClientModal,
          QuickAddProjectModal,
          ClientsView,
          ProjectsView,
          EstimationsView
        },

        data(){
            return{
                users: [],
                selectedUser: null,

                newClient: {
                    name: '',
                    email: '',
                    phone: ''
                },
                clients: [],
                isEditingClient: false,
                editClientId: null,

                newProject:{
                  name: '',
                  description: '',
                  client_id: null
                },
                projects: [],
                isEditingProject: false,
                editProjectId: null,

                newEstimation:{
                  title: '',
                  type:'fixed',
                  hours:null,
                  hourly_rate:null,
                  price: '',
                  project_id: null,
                  client_id: null,
                },
                estimations: [],
                isEditingEstimation: false,
                editEstimationId: null,

                currentView: 'estimations',
                isClientDialogOpen: false,
                isProjectDialogOpen: false,

                filters: {
                  client_id:null,
                  project_id:null,
                  date_from:'',
                  date_to:''

                },
            }
        },

        computed: {
          filteredProjectsForEstimation(){
            if(!this.newEstimation.client_id) return [];
            
            return this.projects.filter(projects => projects.client_id == this.newEstimation.client_id);
          },

          filteredProjectsForSearch(){
            if(!this.filters.client_id) return this.projects;
            return this.projects.filter(p => p.client_id === this.filters.client_id);
          },

          processedEstimations() {
            let result = [...this.estimations];

            // Filtr: Klient
            if (this.filters.client_id) {
                result = result.filter(e => e.project?.client_id === this.filters.client_id);
            }
            // Filtr: Projekt
            if (this.filters.project_id) {
                result = result.filter(e => e.project_id === this.filters.project_id);
            }
            // Filtr: Data Od
            if (this.filters.date_from) {
                const from = new Date(this.filters.date_from);
                result = result.filter(e => new Date(e.created_at) >= from);
            }
            // Filtr: Data Do
            if (this.filters.date_to) {
                const to = new Date(this.filters.date_to);
                to.setHours(23, 59, 59, 999); // Ustawiamy na koniec dnia
                result = result.filter(e => new Date(e.created_at) <= to);
            }

            
            return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          },

          summaryTotalPrice() {
            return this.processedEstimations.reduce((sum, e) => sum + parseFloat(e.price || 0), 0);
          }
          
        },

        mounted(){
            this.fetchUsers();
        },
        methods: {
            async fetchUsers() {
               try {
                    this.users = await api.getUsers();
                } catch (error) {
                    console.error("Błąd pobierania userów:", error);
                }
            },

            async fetchClients() {
                try {
                    const data = await api.getClients();
                    this.clients = Array.isArray(data) ? data : [];
                } catch (error) {
                    console.error(error);
                }
            },

            async fetchProjects() {
                try {
                    const data = await api.getProjects();
                    this.projects = Array.isArray(data) ? data : [];
                } catch (error) {
                    console.error(error);
                }
            },

            async fetchEstimations() {
                try {
                    const data = await api.getEstimations();
                    this.estimations = Array.isArray(data) ? data : [];
                } catch (error) {
                    console.error("Blad 500: ", error);
                }
            },

            async loginAs(user) {
                try {
                    await api.login(user.id);
                    this.selectedUser = user;
                    this.fetchClients();
                    this.fetchProjects();
                    this.fetchEstimations();
                } catch (error) {
                    console.error("Szczegóły błędu logowania:", error);
                }
            },

            async logout() {
                try {
                    await api.logout();
                    this.selectedUser = null;
                } catch (error) {
                    console.error(error);
                    this.selectedUser = null;
                }
            },
            
            async saveClient() {
                const payload = {
                    name: this.newClient.name,
                    email: this.newClient.email,
                    phone: this.newClient.phone,
                };
                
                try {
                    await api.saveClient(payload, this.isEditingClient ? this.editClientId : null);
                    this.cancelEditClient();
                    this.fetchClients();
                    this.fetchProjects();
                    this.fetchEstimations();
                } catch (error) {
                    console.error(error);
                    alert('Error: This client name might already exist for this user!');
                }
            },

            editClient(client){
              this.isEditingClient = true;
              this.editClientId = client.id;
              this.newClient = {
                name: client.name,
                email: client.email,
                phone: client.phone
              };
              document.getElementById('client-form').scrollIntoView({ behavior: 'smooth' });             
            },

            cancelEditClient(){
              this.isEditingClient=false;
              this.editClientId=null;
              this.newClient = {name: '', email: '', phone: ''}; 
            },

            async deleteClient(id) {
                if(confirm("Are you sure you want to delete this client?")) {
                    try {
                        await api.deleteClient(id);
                        this.fetchClients();
                        this.fetchProjects();
                        this.fetchEstimations();
                    } catch (error) {
                        alert("ERROR: Could not delete the client");
                    }
                }
            },

            async saveProject() {
                const payload = {
                    name: this.newProject.name,
                    description: this.newProject.description,
                    client_id: this.newProject.client_id
                };
                
                try {
                    await api.saveProject(payload, this.isEditingProject ? this.editProjectId : null);
                    this.cancelEditProject();
                    this.fetchClients();
                    this.fetchProjects();
                    this.fetchEstimations();
                } catch (error) {
                    console.error(error);
                    alert('Error: This project name might already exist for this client!');
                }
            },

            editProject(project){
              this.isEditingProject = true;
              this.editProjectId = project.id;
              this.newProject = {
                name: project.name,
                description: project.description,
                client_id: project.client_id
              };

              document.getElementById('project-form').scrollIntoView({ behavior: 'smooth' });             
            },

            cancelEditProject(){
              this.isEditingProject=false;
              this.editProjectId=null;
              this.newProject = {name: '', description: '', client_id: null}; 
            },

            async deleteProject(id) {
                if(confirm("Are you sure you want to delete this project?")) {
                    try {
                        await api.deleteProject(id);
                        this.fetchClients();
                        this.fetchProjects();
                        this.fetchEstimations();
                    } catch (error) {
                        alert("ERROR: Could not delete the project");
                    }
                }
            },
            
            async saveEstimation() {
                const payload = {
                    project_id: this.newEstimation.project_id,
                    title: this.newEstimation.title,
                    type: this.newEstimation.type
                };

                if (this.newEstimation.type === 'fixed') {
                    payload.price = this.newEstimation.price;
                } else {
                    payload.hours = this.newEstimation.hours;
                    payload.hourly_rate = this.newEstimation.hourly_rate;
                }

                try {
                    await api.saveEstimation(payload, this.isEditingEstimation ? this.editEstimationId : null);
                    this.cancelEditEstimation();
                    this.fetchEstimations();
                } catch (error) {
                    console.error(error);
                    alert("ERROR: Check if fields are correct");
                }
            },

          editEstimation(estimation){
            this.isEditingEstimation = true;
            this.editEstimationId = estimation.id;
            this.newEstimation = {
              client_id: estimation.project?.client_id,
              project_id: estimation.project_id,
              title: estimation.title,
              type: estimation.type || 'fixed',
              hours: estimation.hours,
              hourly_rate: estimation.hourly_rate,
              price: estimation.price,
            };
            document.getElementById('estimation-form').scrollIntoView({ behavior: 'smooth' });

          },

          cancelEditEstimation(){
            this.isEditingEstimation = false;
            this.editEstimationId = null;
            this.newEstimation ={client_id: null, project_id:null, title:'', type:'fixed', hours:null, hourly_rate:null,  price:''};
          },

          async deleteEstimation(id) {
                if(confirm("Are you sure you want to delete this estimation?")) {
                    try {
                        await api.deleteEstimation(id);
                        this.fetchEstimations();
                    } catch (error) {
                        alert("ERROR: Could not delete estimation");
                    }
                }
            },

          quickSaveClient(){
            const savedName = this.newClient.name;
            this.saveClient();
            setTimeout(() => {
              const found = this.clients.find(c => c.name===savedName);

              if(found){
                if(this.currentView === 'estimations'){
                  this.newEstimation.client_id=found.id;
                } else if(this.currentView === 'projects'){
                  this.newProject.client_id=found.id;
                }
              }
              this.isClientDialogOpen = false;
            }, 500);
          },

          openProjectDialog(){
            this.newProject.client_id = this.newEstimation.client_id;
            this.isProjectDialogOpen = true;
          },

          openClientDialog() {
              this.newClient = { name: '', email: '', phone: '' };
              this.isClientDialogOpen = true;
            },

          quickSaveProject(){
            const savedName = this.newProject.name;
            this.saveProject();
            setTimeout(()=>{
              const found= this.projects.find(p=>p.name===savedName);
              if(found){
                this.newEstimation.project_id = found.id;
              }
              this.isProjectDialogOpen = false;
            }, 500);
          },

          handleQuickAddClient(payload){
            this.newClient = payload;
            this.quickSaveClient();
          },

          handleQuickAddProject(payload) {
            this.newProject.name = payload.name;
            this.newProject.description = payload.description;
            this.quickSaveProject();
          },
            
        }          
      } 

</script>