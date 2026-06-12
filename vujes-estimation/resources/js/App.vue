<template>
    <v-app>
        <v-container v-if="selectedUser==null">
            <h2 class="mb-4 text-h5">Select your profile</h2>

            <v-list lines="two" border>
                <v-list-item
                v-for="user in users"
                :key="user.id"
                :title="user.name"
                :subtitle="user.email"
                @click="loginAs(user)"
                >

                </v-list-item>
            </v-list>


        </v-container>

        <template v-else>
          <v-navigation-drawer permanent app>
            <v-list-item 
            title="Estimation App"
            :subtitle="`User: ${selectedUser.name}`"
            class="py-4"></v-list-item>
            <v-divider></v-divider>

            <v-list density="compact" nav>
              <v-list-item
              prepend-icon="mdi-calculator"
              title="Estimations"
              value="estimations"
              @click="currentView = 'estimations'"
              :active="currentView==='estimations'">
              </v-list-item>

              <v-list-item
              prepend-icon="mdi-briefcase"
              title="Projects"
              value="projects"
              @click="currentView = 'projects'"
              :active="currentView==='projects'">
              </v-list-item>

              <v-list-item
              prepend-icon="mdi-account-group"
              title="Clients"
              value="clients"
              @click="currentView = 'clients'"
              :active="currentView==='clients'">
              </v-list-item>

            </v-list>

            <template v-slot:append>
              <div class="pa-4">
                <v-btn block color="error" variant="outlined" @click="logout()">Change User</v-btn>
              </div>
            </template>
            </v-navigation-drawer>

            <v-main class="bg-grey-lighten-4">
              <v-container class="pa-6">
                <div v-if="currentView==='estimations'">
                  <h2 class="text-h4 mb-4">Estimations</h2>
                  <v-card class="pa-5 mb-5" variant="outlined" id="estimation-form">
                    <v-card-title class="px-0">{{ isEditingEstimation ? "Edit Estimation" : "Add New Estimation" }}
                    </v-card-title>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-select
                          v-model="newEstimation.client_id"
                          :items="clients"
                          item-title="name"
                          item-value="id"
                          label="1. Select Client *"
                          density="compact"
                          hide-details
                          @update:modelValue="newEstimation.project_id = null" 
                        >
                          <template v-slot:append>
                            <v-btn color="primary" variant="tonal" size="small" @click="openClientDialog()" height="40">
                              + New
                            </v-btn>
                          </template>
                        </v-select>
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-select
                          v-model="newEstimation.project_id"
                          :items="filteredProjectsForEstimation" 
                          item-title="name"
                          item-value="id"
                          label="2. Select Project *"
                          density="compact"
                          hide-details
                          :disabled="!newEstimation.client_id"
                        >
                          <template v-slot:append>
                            <v-btn 
                              color="primary" variant="tonal" size="small" 
                              @click="openProjectDialog()"
                              :disabled="!newEstimation.client_id"
                              height="40"
                            >
                              + New
                            </v-btn>
                          </template>
                        </v-select>
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-text-field 
                          v-model="newEstimation.title" 
                          label="Estimation Title *" 
                          density="compact" 
                          hide-details
                          :disabled="!newEstimation.project_id"
                        ></v-text-field>
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-select
                          v-model="newEstimation.type"
                          :items="[{title: 'Fixed Price', value: 'fixed'}, {title: 'Hourly Rate', value: 'hourly'}]"
                          label="Pricing Type *"
                          density="compact"
                          hide-details
                          :disabled="!newEstimation.project_id"
                        ></v-select>
                      </v-col>

                      <v-col cols="12" md="6" v-if="newEstimation.type === 'fixed'">
                        <v-text-field 
                          v-model="newEstimation.price" 
                          label="Total Price (PLN) *" 
                          type="number" 
                          density="compact" 
                          hide-details
                          :disabled="!newEstimation.project_id"
                        ></v-text-field>
                      </v-col>

                      <v-col cols="12" md="6" v-if="newEstimation.type === 'hourly'">
                        <v-text-field 
                          v-model="newEstimation.hours" 
                          label="Estimated Hours *" 
                          type="number" 
                          density="compact" 
                          hide-details
                          :disabled="!newEstimation.project_id"
                        ></v-text-field>
                      </v-col>
                      
                      <v-col cols="12" md="6" v-if="newEstimation.type === 'hourly'">
                        <v-text-field 
                          v-model="newEstimation.hourly_rate" 
                          label="Hourly Rate (PLN) *" 
                          type="number" 
                          density="compact" 
                          hide-details
                          :disabled="!newEstimation.project_id"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <div class="d-flex gap-2 mt-4">
                      <v-btn color="success" @click="saveEstimation()">
                        {{ isEditingEstimation ? "Update Estimation" : "Save Estimation" }}
                      </v-btn>
                      <v-btn v-if="isEditingEstimation" color="dark-grey" variant="text" class="ml-2" @click="cancelEditEstimation()">
                        Cancel
                      </v-btn>

                    </div>
                </v-card>

                <v-card class="mt-5 pa-4 bg-grey-lighten-4" variant="flat">
                    <v-row>
                      <v-col cols="12" md="3">
                        <v-select 
                          v-model="filters.client_id" :items="clients" item-title="name" item-value="id" 
                          label="Filter by Client" density="compact" hide-details clearable
                          @update:modelValue="filters.project_id = null"
                        ></v-select>
                      </v-col>
                      <v-col cols="12" md="3">
                        <v-select 
                          v-model="filters.project_id" :items="filteredProjectsForSearch" item-title="name" item-value="id" 
                          label="Filter by Project" density="compact" hide-details clearable
                        ></v-select>
                      </v-col>
                      <v-col cols="12" md="3">
                        <v-text-field 
                          v-model="filters.date_from" label="Date From" type="date" density="compact" hide-details clearable
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="3">
                        <v-text-field 
                          v-model="filters.date_to" label="Date To" type="date" density="compact" hide-details clearable
                        ></v-text-field>
                      </v-col>
                    </v-row>

                    <v-divider class="my-4"></v-divider>
                    
                    <div class="d-flex justify-space-between align-center">
                      <span class="text-subtitle-1 font-weight-medium text-grey-darken-1">
                        Showing {{ processedEstimations.length }} estimation(s)
                      </span>
                      <span class="text-h5 font-weight-bold text-success">
                        Total Value: {{ summaryTotalPrice.toLocaleString('pl-PL') }} PLN
                      </span>
                    </div>
                  </v-card>

                <v-card class="mt-5 pa-5" variant="outlined">
                  <v-card-title class="px-0">Estimation List</v-card-title>
                  <v-table>
                      <thead>
                        <tr>
                          <th class="text-center font-weight-bold">Date</th>
                          <th class="text-center font-weight-bold">Client Name</th> 
                          <th class="text-center font-weight-bold">Project Name</th>
                          <th class="text-center font-weight-bold">Estimation Title</th>
                          <th class="text-center font-weight-bold">Price</th>
                          <th class="text-center font-weight-bold">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="estimation in processedEstimations" :key="estimation.id">
                          <td class="text-center">{{ formatDate(estimation.created_at) }}</td>
                          <td class="text-center">{{ estimation.project?.client?.name }}</td>
                          <td class="text-center">{{ estimation.project?.name }}</td>
                          <td class="text-center">{{ estimation.title }}</td>
                          <td class="text-center">
                            <span v-if="estimation.type==='hourly'">
                              <strong>{{ estimation.price }} PLN</strong> <br>
                              <small class="text-grey">({{ estimation.hours }}h @ {{ estimation.hourly_rate }} PLN/h)</small>
                            </span>
                            <span v-else>
                              <strong>{{ estimation.price }} PLN</strong> <br>
                              <small class="text-grey">(Fixed Price)</small>
                            </span>
                            </td>
                          <td class="text-center">
                            <v-btn color="warning" size="small" @click="editEstimation(estimation)">Edit</v-btn>
                            <v-btn color="error" size="small" class="ml-3" @click="deleteEstimation(estimation.id)">Delete</v-btn>
                          </td>
                        </tr>
                      </tbody>
                    </v-table>
                </v-card>

                
                </div>

                <div v-if="currentView === 'projects'">
                <h2 class="text-h4 mb-4">Projects</h2>
                  <v-card class="pa-5 mb-5" variant="outlined" id="project-form">
        <v-card-title class="px-0">
          {{ this.isEditingProject ? 'Edit Project' : 'Add New Project' }}
        </v-card-title>
        
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="newProject.name" label="Name" density="compact" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="newProject.description" label="Description" type="text" density="compact" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="4" class="d-flex align-center">
            <v-select
            v-model="newProject.client_id"
            :items="clients"
            item-title="name"
            item-value="id"
            label="Select Client *"
            density="compact"
            hide-details
            ></v-select>

            <v-btn 
            color="primary"
            variant="tonal"
            icon="mdi-plus"
            size="small"
            class="ml-2"
            @click="openClientDialog()"
            title="Add New Client"></v-btn>
          </v-col>
        </v-row>
        
        <div class="d-flex gap-2">
        <v-btn color="success" class="mt-5" @click="saveProject()">
          {{ this.isEditingProject ? 'Update Project' : 'Save Project' }}
        </v-btn>
        <v-btn v-if="this.isEditingProject" color="dark-grey" variant="text" class="ml-2 mt-5" @click="cancelEditProject()">
          Cancel
        </v-btn>
        </div>
        </v-card>

        <v-card class="mt-5 pa-5" variant="outlined">
        <v-card-title class="px-0">Project List</v-card-title>
        <v-table>
          <thead>
            <tr>
              <th class="text-center font-weight-bold">Name</th>
              <th class="text-center font-weight-bold">Description</th>
              <th class="text-center font-weight-bold">temp</th>
              <th class="text-center font-weight-bold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in projects" :key="project.id">
              <td class="text-center">{{ project.name }}</td>
              <td class="text-center">{{project.description}}</td>
              <td class="text-center">{{ project.client?.name }}</td>
              <td class="text-center"><v-btn color='warning' size='small' @click='editProject(project)'>Edit</v-btn>
                <v-btn color="error" size="small" class="ml-3" @click="deleteProject(project.id)">Delete</v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>


        </v-card>
              </div>

              <div v-if="currentView === 'clients'">
                <h2 class="text-h4 mb-4">Clients</h2>
                <v-card class="pa-5 mb-5" variant="outlined" id="client-form">
        <v-card-title class="px-0">
          {{ this.isEditingClient ? 'Edit Client' : 'Add New Client' }}
        </v-card-title>
        
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="newClient.name" label="Name" density="compact"></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="newClient.email" label="Email" type="email" density="compact"></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="newClient.phone" label="Phone" density="compact"></v-text-field>
          </v-col>
        </v-row>
        
        <div class="d-flex gap-2">
        <v-btn color="success" @click="saveClient()">
          {{ this.isEditingClient ? 'Update Client' : 'Save Client' }}
        </v-btn>
        <v-btn v-if="this.isEditingClient" color="dark-grey" variant="text" class="ml-2" @click="cancelEditClient()">
          Cancel
        </v-btn>
        </div>
        </v-card>

        <v-card class="mt-5 pa-5" variant="outlined">
        <v-card-title class="px-0">Client List</v-card-title>
        <v-table>
          <thead>
            <tr>
              <th class="text-center font-weight-bold">Name</th>
              <th class="text-center font-weight-bold">Email</th>
              <th class="text-center font-weight-bold">Phone</th>
              <th class="text-center font-weight-bold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in clients" :key="client.id">
              <td class="text-center">{{ client.name }}</td>
              <td class="text-center">{{client.email}}</td>
              <td class="text-center">{{ client.phone }}</td>
              <td class="text-center"><v-btn color='warning' size='small' @click='editClient(client)'>Edit</v-btn>
                <v-btn color="error" size="small" class="ml-3" @click="deleteClient(client.id)">Delete</v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>


        </v-card>
        </div>
        <v-dialog v-model="isClientDialogOpen" max-width="500px">
                  <v-card>
                    <v-card-title class="pa-4 text-h5">Quick Add Client</v-card-title>
                    <v-card-text class="pt-0">
                      <v-text-field v-model="newClient.name" label="Name *" density="compact" class="mb-2"></v-text-field>
                      <v-text-field v-model="newClient.email" label="Email" type="email" density="compact" class="mb-2"></v-text-field>
                      <v-text-field v-model="newClient.phone" label="Phone" density="compact" class="mb-2"></v-text-field>
                    </v-card-text>
                    <v-card-actions class="pa-4 pt-0">
                      <v-spacer></v-spacer>
                      <v-btn color="error" variant="text" @click="isClientDialogOpen=false">Cancel</v-btn>
                      <v-btn color="success" variant="flat" @click="quickSaveClient()">Save Client</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>

                <v-dialog v-model="isProjectDialogOpen" max-width="500px">
                    <v-card>
                      <v-card-title class="pa-4 text-h5">Quick Add Project</v-card-title>
                      <v-card-text class="pt-0">
                        <v-text-field v-model="newProject.name" label="Project Name *" density="compact" class="mb-3"></v-text-field>
                        <v-text-field v-model="newProject.description" label="Description" density="compact" class="mb-3"></v-text-field>
                      </v-card-text>
                      <v-card-actions class="pa-4 pt-0">
                        <v-spacer></v-spacer>
                        <v-btn color="error" variant="text" @click="isProjectDialogOpen = false">Cancel</v-btn>
                        <v-btn color="success" variant="flat" @click="quickSaveProject()">Save Project</v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
        </v-container>
        </v-main>

      </template>
    </v-app>   
</template>
 
<script>
    export default{
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
            fetchUsers() {
               fetch('/api/users')
               .then(response => response.json())
               .then(data => {
                this.users = data;
               }); 
            },

            fetchClients(){
              fetch(`/api/clients?user_id=${this.selectedUser.id}`)
              .then(response => response.json())
              .then(data =>{
                this.clients = data;
              });
            },

            fetchProjects(){
              fetch(`/api/projects?user_id=${this.selectedUser.id}`)
              .then(response=> response.json())
              .then(data => {
                this.projects=data;
              })
            },

            fetchEstimations(){
              fetch(`/api/estimations?user_id=${this.selectedUser.id}`)
              .then(response=> response.json())
              .then(data => {
                if(Array.isArray(data)){
                this.estimations = data;
                } else{
                  console.error("Blad 500: ", data);
                }
              })
            },

            loginAs(user){
                this.selectedUser = user;
                this.fetchClients();
                this.fetchProjects();
                this.fetchEstimations();
            },

            logout(){
                this.selectedUser = null;
            },

            
            saveClient() {
              const payload = {
                name: this.newClient.name,
                email: this.newClient.email,
                phone: this.newClient.phone,
                user_id: this.selectedUser.id
              };
              
              const url = this.isEditingClient ? `/api/clients/${this.editClientId}` : '/api/clients';
              let finalPayload = {...payload};
              if(this.isEditingClient){
                finalPayload._method = 'PUT';
              }

              fetch(url, {
                method: 'POST',
                headers: { 
                  'Content-Type': 'application/json', 
                  'Accept': 'application/json',
                  'X-HTTP-Method-Override': this.isEditingClient ? 'PUT' : 'POST' 
                },
                body: JSON.stringify(finalPayload)
              })
              .then(async response => {
                if (response.ok) {
                  this.cancelEditClient();
                  this.fetchClients();
                  this.fetchProjects();
                  this.fetchEstimations();
                } else {
                    const errorData = await response.json();
                  console.error(errorData)
                  alert('Error: This client name might already exist for this user!');
                }
                });
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

            deleteClient(id){
              if(confirm("Are you sure you want to delete this client?")){
                fetch(`/api/clients/${id}`, {
                  method: 'POST', 
                  headers: {
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE' 
                  },
                  body: JSON.stringify({
                    _method: 'DELETE'
                  })
                })
                .then(response =>{
                  if(response.ok){
                    this.fetchClients();
                    this.fetchProjects();
                    this.fetchEstimations();
                  }
                  else{
                    alert("ERROR: Could not delete the client");
                  }
                });
              }
            
            },
            saveProject() {
              const payload = {
                name: this.newProject.name,
                description: this.newProject.description,
                client_id: this.newProject.client_id
              };
              
              const url = this.isEditingProject ? `/api/projects/${this.editProjectId}` : '/api/projects';
              let finalPayload = {...payload};
              if(this.isEditingProject){
                finalPayload._method = 'PUT';
              }

              fetch(url, {
                method: 'POST',
                headers: { 
                  'Content-Type': 'application/json', 
                  'Accept': 'application/json',
                  'X-HTTP-Method-Override': this.isEditingProject ? 'PUT' : 'POST'
                },
                body: JSON.stringify(finalPayload)
              })
              .then(async response => {
                if (response.ok) {
                  this.cancelEditProject();
                  this.fetchClients();
                  this.fetchProjects();
                  this.fetchEstimations();
                } else {
                    const errorData = await response.json();
                  console.error(errorData)
                  alert('Error: This project name might already exist for this client!');
                }
                });
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

            deleteProject(id){
              if(confirm("Are you sure you want to delete this project?")){
                fetch(`/api/projects/${id}`, {
                  method: 'POST', 
                  headers: {
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE' 
                  },
                  body: JSON.stringify({
                    _method: 'DELETE'
                  })
                })
                .then(response =>{
                  if(response.ok){
                    this.fetchClients();
                    this.fetchProjects();
                    this.fetchEstimations();
                  }
                  else{
                    alert("ERROR: Could not delete the project");
                  }
                });
              }
            },
            
            saveEstimation(){
              const payload = {
                project_id: this.newEstimation.project_id,
                title: this.newEstimation.title,
                type: this.newEstimation.type
              };

              if(this.newEstimation.type==='fixed'){
                payload.price = this.newEstimation.price;
              } else{
                payload.hours = this.newEstimation.hours;
                payload.hourly_rate = this.newEstimation.hourly_rate;
              }

              const url = this.isEditingEstimation ? `/api/estimations/${this.editEstimationId}` : '/api/estimations';
              let finalPayload = {...payload};
              if(this.isEditingEstimation){
                finalPayload._method = 'PUT';
              }

              fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'Accept': 'application/json',
                  'X-HTTP-Method-Override': this.isEditingEstimation ? 'PUT' : 'POST'
                },
                body: JSON.stringify(finalPayload)

              })
              .then(async response => {
                if (response.ok){
                  this.cancelEditEstimation();
                  this.fetchEstimations();
                } else {
                  const errorData = await response.json();
                  console.error(errorData);
                  alert("ERROR: Check if fields are correct");
              }
            });
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

          deleteEstimation(id){
            if(confirm("Are you sure you want to delete this estimation?")){
              fetch(`/api/estimations/${id}`, {
                  method: 'POST', 
                  headers: {
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE' 
                  },
                  body: JSON.stringify({
                    _method: 'DELETE'
                  })
                })
              .then(response => {
                if(response.ok){
                  this.fetchEstimations();
                } else {
                  alert("ERROR: Could not delete estimation");
                }
              });
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

          formatDate(dateString){
            if(!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('pl-PL', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric'
            });
          }
            

        }
            
      } 

</script>

<style>

</style>