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

        <v-container v-else>
            <h2>Estimations Dashboard</h2>
            <p>Logged in as <strong>{{ selectedUser.name }}</strong></p>

            <v-btn color="error" class="mb-5" @click="logout()"> Change User</v-btn>

        <v-card class="pa-5 mb-5" variant="outlined" id="client-form">
        <v-card-title class="px-0">
          {{ isEditing ? 'Edit Client' : 'Add New Client' }}
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
          {{ isEditing ? 'Update Client' : 'Save Client' }}
        </v-btn>
        <v-btn v-if="isEditing" color="dark-grey" variant="text" class="ml-2" @click="cancelEdit()">
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
        </v-container>
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
                isEditing: false,
                editClientId: null
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

            loginAs(user){
                this.selectedUser = user;
                this.fetchClients();
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
              
              const url = this.isEditing ? `/api/clients/${this.editClientId}` : '/api/clients';
              const httpMethod = this.isEditing ? 'PUT' : 'POST';

              fetch(url, {
                method: httpMethod,
                headers: { 
                  'Content-Type': 'application/json', 
                  'Accept': 'application/json' 
                },
                body: JSON.stringify(payload)
              })
              .then(async response => {
                if (response.ok) {
                  this.cancelEdit();
                  this.fetchClients();
                } else {
                    const errorData = await response.json();
                  console.error(errorData)
                  alert('Error: This client name might already exist for this user!');
                }
                });
              },

            editClient(client){
              this.isEditing = true;
              this.editClientId = client.id;
              this.newClient = {
                name: client.name,
                email: client.email,
                phone: client.phone
              };

              document.getElementById('client-form').scrollIntoView({ behavior: 'smooth' });             
            },

            cancelEdit(){
              this.isEditing=false;
              this.editClientId=null;
              this.newClient = {name: '', email: '', phone: ''}; 
            },

            deleteClient(id){
              if(confirm("Are you sure you want to delete this client?")){
                fetch(`/api/clients/${id}`, {
                  method: 'DELETE',
                  headers: {
                    'Accept': 'application/json'
                  }
                })
                .then(response =>{
                  if(response.ok){
                    this.fetchClients();
                  }
                  else{
                    alert("ERROR: Could not delete the client");
                  }
                });
              }
            
            }


            }
          } 

</script>

<style>

</style>