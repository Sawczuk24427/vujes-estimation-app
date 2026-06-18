<template>
    <div>
    <h2 class="text-h4 mb-4">Projects</h2>
                  <v-card class="pa-5 mb-5" variant="outlined" id="project-form">
        <v-card-title class="px-0">
          {{ isEditing ? 'Edit Project' : 'Add New Project' }}
        </v-card-title>
        
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="projectForm.name" label="Name" density="compact" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="projectForm.description" label="Description" type="text" density="compact" hide-details></v-text-field>
          </v-col>
          <v-col cols="12" md="4" class="d-flex align-center">
            <v-select
            v-model="projectForm.client_id"
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
            @click="$emit('open-client-dialog')"
            title="Add New Client"></v-btn>
          </v-col>
        </v-row>
        
        <div class="d-flex gap-2">
        <v-btn color="success" class="mt-5" @click="$emit('save')">
          {{ isEditing ? 'Update Project' : 'Save Project' }}
        </v-btn>
        <v-btn v-if="isEditing" color="dark-grey" variant="text" class="ml-2 mt-5" @click="$emit('cancel')">
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
              <td class="text-center"><v-btn color='warning' size='small' @click="$emit('edit', project)">Edit</v-btn>
                <v-btn color="error" size="small" class="ml-3" @click="$emit('delete', project.id)">Delete</v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>


        </v-card> 

    </div>
</template>

<script>

    export default {
        name: 'ProjectView',
        props: {
            projects: {
                type:Array,
                required:true
            },

            clients: {
                type:Array,
                required: true
            },
            projectForm: {
                type:Object,
                required:true
            },

            isEditing: {
                type:Boolean,
                required:true
            }
        },

        emits: ['save', 'cancel', 'edit', 'delete', 'open-client-dialog']
    }

</script>