<template>
  <div id="ticket-rvcl-collapse-card" class="ticket-collapse-card" :class="toDoCheckList.className">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title> Verification Checklist </b-card-title>
      <hr />
      <b-list-group v-if="toDoCheckList.list.length">
        <template v-for="(toDoItem, i) of toDoCheckList.list">
          <b-list-group-item :key="i">
            <b-form-checkbox v-model="toDoCheckList.checkedItems" inline class="reminder" name="to-do-checklist" :value="toDoItem.itemId">
              {{ toDoItem.itemName }}
              <strong v-if="toDoItem.checkedOn">
                ({{ toDoItem.checkedOn | dateFormat('MM/DD/YYYY') }} @ {{ toDoItem.checkedOn | dateFormat('hh:mm a') }} by
                {{ toDoItem.modified_by }})
              </strong>
            </b-form-checkbox>
          </b-list-group-item>
        </template>
      </b-list-group>
      <b-row v-if="role.internalKey === PREKEY" class="mt-1">
        <b-col>
          <b-form inline>
            <label> Smartborder Entry Number: </label>
            <b-form-input v-model="form.sbFilterCode" class="ml-1 col-sm-1" />
            <label class="form-divider h5"> - </label>
            <b-form-input v-model="form.sbEntryNum" placeholder="9999999-9" class="col-sm-3" />
            <b-button class="ml-1 float-right" variant="success" @click.prevent>Update Checklist / Entry # </b-button>
          </b-form>
        </b-col>
      </b-row>
    </b-card>
  </div>
</template>

<script>
/* eslint-disable no-nested-ternary */
/* eslint-disable indent */
import { BCard, BCardTitle, BListGroup, BListGroupItem, BFormCheckbox, BRow, BCol, BForm, BFormInput, BButton } from 'bootstrap-vue'

import { dateFormat } from '@/utils/filters'
import { PREKEY } from '@/utils/roles'

export default {
  components: {
    BCard,
    BCardTitle,
    BListGroup,
    BListGroupItem,
    BFormCheckbox,
    BRow,
    BCol,
    BForm,
    BFormInput,
    BButton,
  },
  filters: {
    dateFormat,
  },
  props: {
    getFullTicket: {
      type: Object,
      required: true,
    },
    role: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      PREKEY,
      form: {
        sbFilterCode: '8PY',
        sbEntryNum: this.getFullTicket.SBEntryNum,
      },
    }
  },
  computed: {
    toDoCheckList() {
      const toDoCheckList = this.getFullTicket.to_do_checklist || []
      const checkedItems = toDoCheckList.reduce((accum, i) => {
        if (i.doneId) {
          accum.push(+i.itemId)
        }
        return accum
      }, [])
      const className = !checkedItems.length
        ? 'ticket-rvcl-collapse-card-danger'
        : checkedItems.length === toDoCheckList.length
        ? 'ticket-rvcl-collapse-card-success'
        : 'ticket-rvcl-collapse-card-warning'
      return {
        className,
        list: toDoCheckList,
        checkedItems,
      }
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/base/bootstrap-extended/include';

#ticket-rvcl-collapse-card {
  &.ticket-rvcl-collapse-card-danger {
    background-color: rgba($danger, 0.3);
  }
  &.ticket-rvcl-collapse-card-warning {
    background-color: rgba($yellow, 0.3);
  }
  &.ticket-rvcl-collapse-card-success {
    background-color: rgba($success, 0.3);
  }
  .list-group-item {
    background-color: unset;
  }
  .form-divider {
    margin-left: 5px;
    margin-right: 5px;
  }
}
</style>
