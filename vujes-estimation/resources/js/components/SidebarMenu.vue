<template>
  <v-btn
    v-if="!drawerVisible"
    class="d-md-none"
    color="primary"
    icon
    elevation="8"
    style="position: fixed; top: 20px; left: 20px; z-index: 9999"
    @click="drawerVisible = true"
  >
    <v-icon>mdi-menu</v-icon>
  </v-btn>

  <v-navigation-drawer v-model="drawerVisible" app>
    <v-list-item
      title="Estimation App"
      :subtitle="`User: ${user?.name}`"
      class="py-4"
    ></v-list-item>
    <v-divider></v-divider>

    <v-list density="compact" nav>
      <v-list-item
        prepend-icon="mdi-calculator"
        title="Estimations"
        value="estimations"
        @click="$emit('change-view', 'estimations')"
        :active="currentView === 'estimations'"
      >
      </v-list-item>

      <v-list-item
        prepend-icon="mdi-briefcase"
        title="Projects"
        value="projects"
        @click="$emit('change-view', 'projects')"
        :active="currentView === 'projects'"
      >
      </v-list-item>

      <v-list-item
        prepend-icon="mdi-account-group"
        title="Clients"
        value="clients"
        @click="$emit('change-view', 'clients')"
        :active="currentView === 'clients'"
      >
      </v-list-item>
    </v-list>

    <template v-slot:append>
      <div class="pa-4">
        <v-btn block color="error" variant="outlined" @click="$emit('logout')">Change User</v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>

<script>
  export default {
    name: 'SidebarMenu',
    props: {
      user: {
        type: Object,
        required: true,
      },
      currentView: {
        type: String,
        required: true,
      },
    },

    emits: ['change-view', 'logout'],
    data() {
      return {
        drawerVisible: null,
      };
    },
  };
</script>
