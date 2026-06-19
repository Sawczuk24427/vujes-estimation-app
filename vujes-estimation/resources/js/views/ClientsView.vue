<template>
  <div>
    <h2 class="text-h4 mb-4">Clients</h2>
    <v-card class="pa-5 mb-5" variant="outlined" id="client-form">
      <v-card-title class="px-0">
        {{ isEditing ? 'Edit Client' : 'Add New Client' }}
      </v-card-title>

      <v-row>
        <v-col cols="12" md="4">
          <v-text-field v-model="clientForm.name" label="Name" density="compact"></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="clientForm.email"
            label="Email"
            type="email"
            density="compact"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field v-model="clientForm.phone" label="Phone" density="compact"></v-text-field>
        </v-col>
      </v-row>

      <div class="d-flex gap-2">
        <v-btn color="success" @click="$emit('save')">
          {{ isEditing ? 'Update Client' : 'Save Client' }}
        </v-btn>
        <v-btn
          v-if="isEditing"
          color="dark-grey"
          variant="text"
          class="ml-2"
          @click="$emit('cancel')"
        >
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
            <td class="text-center">{{ client.email }}</td>
            <td class="text-center">{{ client.phone }}</td>
            <td class="text-center">
              <v-btn color="warning" size="small" @click="$emit('edit', client)">Edit</v-btn>
              <v-btn color="error" size="small" class="ml-3" @click="$emit('delete', client.id)"
                >Delete</v-btn
              >
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script>
  export default {
    name: 'ClientsView',

    props: {
      clients: {
        type: Array,
        required: true,
      },

      clientForm: {
        type: Object,
        required: true,
      },
      isEditing: {
        type: Boolean,
        required: true,
      },
    },

    emits: ['save', 'cancel', 'edit', 'delete'],
  };
</script>
