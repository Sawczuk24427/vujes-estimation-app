<template>
  <v-dialog
    :model-value="isOpen"
    @update:model-value="$emit('update:isOpen', $event)"
    max-width="500px"
  >
    <v-card>
      <v-card-title class="pa-4 text-h5">Quick Add Project</v-card-title>
      <v-card-text class="pt-0">
        <v-text-field
          v-model="form.name"
          label="Project Name *"
          density="compact"
          class="mb-3"
        ></v-text-field>
        <v-text-field
          v-model="form.description"
          label="Description"
          density="compact"
          class="mb-3"
        ></v-text-field>
      </v-card-text>
      <v-card-actions class="pa-4 pt-0">
        <v-spacer></v-spacer>
        <v-btn color="error" variant="text" @click="$emit('update:isOpen', false)">Cancel</v-btn>
        <v-btn color="success" variant="flat" @click="handleSave">Save Project</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: 'QuickAddProjectModal',
    props: {
      isOpen: {
        type: Boolean,
        required: true,
      },
    },
    emits: ['update:isOpen', 'save'],
    data() {
      return {
        form: { name: '', description: '' },
      };
    },
    watch: {
      isOpen(newValue) {
        if (newValue) {
          this.form = { name: '', description: '' };
        }
      },
    },
    methods: {
      handleSave() {
        this.$emit('save', { ...this.form });
      },
    },
  };
</script>
