<template>
    <v-container>
        <h2 class="mb-4 text-h5">Select your profile</h2>
        <v-list lines="two" border>
                <v-list-item
                v-for="user in users"
                :key="user.id"
                :title="user.name"
                :subtitle="user.email"
                @click="$emit('login', user)"
                >


                </v-list-item>
            </v-list>
    </v-container>

</template>

<script>

    export default {
        name: 'LoginProfileSelector',
        props: {
            users: {
                type: Array,
                required: true
            }
        },

        emits:['login']
    }

</script>