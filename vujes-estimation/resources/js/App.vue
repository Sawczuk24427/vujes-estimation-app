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

            <v-button color="error" class="mb-5" @click="logout()"> Change User</v-button>

            <v-card class="pa-5" variant="outlined">
        <p>Placeholder text</p>
      </v-card>
        </v-container>
    </v-app>   
</template>

<script>
    export default{
        data(){
            return{
                users: [],
                selectedUser: null
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
            }
        }    
    }
</script>

<style>

</style>