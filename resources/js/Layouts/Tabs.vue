<template>
  <div>
    <nav class="pl-2">
      <div class="nav nav-tabs">
        <a class="nav-item nav-link" role="button" style="cursor: pointer;" v-for="tab in tabs" @click="selectTab(tab.name)" :class="{ 'active': tab.isActive, 'text-secondary': tab.counter == 0 }">
          <i class="mdi" v-if="tab.icon" :class="tab.icon"></i> {{ tab.name }}
          <span class="ml-1 badge badge-pill border-0" v-if="tab.counter"
            :class="{ 'active': tab.isActive, 'badge-info': tab.counter > 0 || tab.counter == null, 'badge-secondary': tab.counter == 0 }">
              {{ tab.counter }}
          </span>
        </a>
      </div>
    </nav>

    <div class="tab-content">
      <div class="card">
        <div class="card-body">
          <slot></slot>
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      id: { type: String, default: null }
    },

    data() {
      return {
        tabsId: '',
        tabs: []
      }
    },

    methods: {
      selectTab(selectedTab) {
        this.tabs.forEach(tab => {
          tab.isActive = (tab.name == selectedTab);
          if (tab.isActive == true) {
            this.updateSelectedTab(tab.name);
          }
        });
      },
      updateSelectedTab(name) {
        this.$emit('selected', name);
        this.$cookies.set(this.tabsId, name, "360s");
      }
    },

    mounted() {
      this.tabsId = this.id + 'Tabs';

      this.tabs = this.$children.filter(child => {
        return child.$el.localName == 'div';
      });

      let anyActive = this.tabs.filter(tab => tab.isActive == true);

      let cookieSelected = this.$cookies.get(this.tabsId);

      if (anyActive.length == 0) {
        if (cookieSelected && cookieSelected.length > 0) {
          this.tabs.forEach(tab => {
            if (tab.name == cookieSelected) {
              tab.isActive = true;
              this.updateSelectedTab(tab.name);
            }
          })
        } else {
          this.tabs[0].isActive = true;
          this.updateSelectedTab(this.tabs[0].name);
        }
      }

      this.tabs.forEach(tab => {
        if (tab.isActive == true) {
          this.updateSelectedTab(tab.name);
        }
      });
    }
  }
</script>
