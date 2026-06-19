<template>
  <div>
    <h2 class="text-h4 mb-4">Estimations</h2>
    <v-card class="pa-5 mb-5" variant="outlined" id="estimation-form">
      <v-card-title class="px-0"
        >{{ isEditing ? 'Edit Estimation' : 'Add New Estimation' }}
      </v-card-title>
      <v-row>
        <v-col cols="12" md="6">
          <v-select
            v-model="estimationForm.client_id"
            :items="clients"
            item-title="name"
            item-value="id"
            label="1. Select Client *"
            density="compact"
            hide-details
            @update:modelValue="estimationForm.project_id = null"
          >
            <template v-slot:append>
              <v-btn
                color="primary"
                variant="tonal"
                size="small"
                @click="$emit('open-client-dialog')"
                height="40"
              >
                + New
              </v-btn>
            </template>
          </v-select>
        </v-col>

        <v-col cols="12" md="6">
          <v-select
            v-model="estimationForm.project_id"
            :items="filteredProjectsForEstimation"
            item-title="name"
            item-value="id"
            label="2. Select Project *"
            density="compact"
            hide-details
            :disabled="!estimationForm.client_id"
          >
            <template v-slot:append>
              <v-btn
                color="primary"
                variant="tonal"
                size="small"
                @click="$emit('open-project-dialog')"
                :disabled="!estimationForm.client_id"
                height="40"
              >
                + New
              </v-btn>
            </template>
          </v-select>
        </v-col>

        <v-col cols="12" md="6">
          <v-text-field
            v-model="estimationForm.title"
            label="Estimation Title *"
            density="compact"
            hide-details
            :disabled="!estimationForm.project_id"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6">
          <v-select
            v-model="estimationForm.type"
            :items="[
              { title: 'Fixed Price', value: 'fixed' },
              { title: 'Hourly Rate', value: 'hourly' },
            ]"
            label="Pricing Type *"
            density="compact"
            hide-details
            :disabled="!estimationForm.project_id"
          ></v-select>
        </v-col>

        <v-col cols="12" md="6" v-if="estimationForm.type === 'fixed'">
          <v-text-field
            v-model="estimationForm.price"
            label="Total Price (PLN) *"
            type="number"
            density="compact"
            hide-details
            :disabled="!estimationForm.project_id"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6" v-if="estimationForm.type === 'hourly'">
          <v-text-field
            v-model="estimationForm.hours"
            label="Estimated Hours *"
            type="number"
            density="compact"
            hide-details
            :disabled="!estimationForm.project_id"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="6" v-if="estimationForm.type === 'hourly'">
          <v-text-field
            v-model="estimationForm.hourly_rate"
            label="Hourly Rate (PLN) *"
            type="number"
            density="compact"
            hide-details
            :disabled="!estimationForm.project_id"
          ></v-text-field>
        </v-col>
      </v-row>
      <div class="d-flex gap-2 mt-4">
        <v-btn color="success" @click="$emit('save')">
          {{ isEditingEstimation ? 'Update Estimation' : 'Save Estimation' }}
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

    <v-card class="mt-5 pa-4 bg-grey-lighten-4" variant="flat">
      <v-row>
        <v-col cols="12" md="3">
          <v-select
            v-model="filters.client_id"
            :items="clients"
            item-title="name"
            item-value="id"
            label="Filter by Client"
            density="compact"
            hide-details
            clearable
            @update:modelValue="filters.project_id = null"
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-select
            v-model="filters.project_id"
            :items="filteredProjectsForSearch"
            item-title="name"
            item-value="id"
            label="Filter by Project"
            density="compact"
            hide-details
            clearable
          ></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="filters.date_from"
            label="Date From"
            type="date"
            density="compact"
            hide-details
            clearable
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            v-model="filters.date_to"
            label="Date To"
            type="date"
            density="compact"
            hide-details
            clearable
          ></v-text-field>
        </v-col>
      </v-row>

      <v-divider class="my-4"></v-divider>

      <div class="d-flex justify-space-between align-center">
        <span class="text-subtitle-1 font-weight-medium text-grey-darken-1">
          Showing {{ estimations.length }} estimation(s)
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
          <tr v-for="estimation in estimations" :key="estimation.id">
            <td class="text-center">{{ estimation.created_at_formatted }}</td>
            <td class="text-center">{{ estimation.project?.client?.name }}</td>
            <td class="text-center">{{ estimation.project?.name }}</td>
            <td class="text-center">{{ estimation.title }}</td>
            <td class="text-center">
              <span v-if="estimation.type === 'hourly'">
                <strong>{{ estimation.price }} PLN</strong> <br />
                <small class="text-grey"
                  >({{ estimation.hours }}h @ {{ estimation.hourly_rate }} PLN/h)</small
                >
              </span>
              <span v-else>
                <strong>{{ estimation.price }} PLN</strong> <br />
                <small class="text-grey">(Fixed Price)</small>
              </span>
            </td>
            <td class="text-center">
              <v-btn color="warning" size="small" @click="$emit('edit', estimation)">Edit</v-btn>
              <v-btn color="error" size="small" class="ml-3" @click="$emit('delete', estimation.id)"
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
    name: 'EstimationsView',
    props: {
      estimations: { type: Array, required: true },
      clients: { type: Array, required: true },
      filteredProjectsForEstimation: { type: Array, required: true },
      filteredProjectsForSearch: { type: Array, required: true },
      estimationForm: { type: Object, required: true },
      filters: { type: Object, required: true },
      isEditing: { type: Boolean, required: true },
      summaryTotalPrice: { type: Number, required: true },
    },

    emits: ['save', 'cancel', 'edit', 'delete', 'open-client-dialog', 'open-project-dialog'],
  };
</script>
