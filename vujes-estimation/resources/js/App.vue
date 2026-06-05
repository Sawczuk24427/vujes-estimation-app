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

            <v-card class="pa-5 mb-5" variant="outlined">
        <v-card-title class="px-0">Add New Client</v-card-title>
        
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

        <v-btn color="success" @click="saveClient()">Save Client</v-btn>
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
                }
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

            loginAs(user){
                this.selectedUser = user;
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

      fetch('/api/clients', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json', 
          'Accept': 'application/json' 
        },
        body: JSON.stringify(payload)
      })
      .then(async response => {
        if (response.ok) {
          this.newClient = { name: '', email: '', phone: '' };
          alert('Client saved!');
        } else {
            const errorData = await response.json();
          console.error(errorData)
          alert('Error: This client name might already exist for this user!');
        }
      });
    },
        }    
    }
</script>

<style>

</style>