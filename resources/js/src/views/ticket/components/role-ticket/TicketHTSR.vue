<template>
  <div id="ticket-htsr-collapse-card" class="ticket-collapse-card" :class="community.className">
    <b-card bg-variant="transparent" border-variant="primary">
      <b-card-title>
        Commodities (HTS Code assignment)
        <b-button v-b-toggle.ticket-htsr-collapse class="float-right" size="sm" variant="outline-success">Add</b-button>
      </b-card-title>
      <hr />
      <b-collapse id="ticket-htsr-collapse">
        <div class="p-1 text-dark">
          <strong> Previously used codes </strong>
          <b-row class="border-x">
            <b-col cols="12" md="3">
              <span>HTS Code</span>
            </b-col>
            <b-col cols="12" md="6">
              <span>Description</span>
            </b-col>
            <b-col cols="12" md="3">
              <span />
            </b-col>
          </b-row>
          <template v-if="htsCodes.length">
            <b-row v-for="(hts, i) of htsCodes" :key="i">
              <b-col cols="12" md="3">
                <span>{{ hts.code }}</span>
              </b-col>
              <b-col cols="12" md="6">
                <span>{{ hts.description }}</span>
              </b-col>
              <b-col cols="12" md="3">
                <b-button v-if="!community.commodity_uhids.includes(hts.id)" size="sm" variant="flat-success" @click.prevent>
                  Add to ticket
                </b-button>
              </b-col>
            </b-row>
          </template>
          <b-row v-else>
            <b-col cols>
              <span>No codes available</span>
            </b-col>
          </b-row>
        </div>
      </b-collapse>
      <div v-if="community.communities.length" class="mt-1">
        <strong class="text-primary"> Commodities for this Shipment </strong>
        <template v-if="htsCodeAssignments.length">
          <b-row v-for="(hca, i) of htsCodeAssignments" :key="i" class="mt-1">
            <b-col cols="12" md="2">
              <span>
                {{ hca.code }}
                <div v-if="hca.BasicDutyRateString">
                  {{ hca.BasicDutyRateString }} <span v-if="hca.USTR301 == 'Y'" class="text-danger"> * </span>
                </div>
              </span>
            </b-col>
            <b-col cols="12" md="8">
              <div>{{ hca.description }}</div>
              <div>{{ hca.mergedDescription }}</div>
              <div v-if="hca.pgacodes"><strong>PGA Code: </strong>{{ hca.pgacodes }}</div>
            </b-col>
            <b-col cols="12" md="2">
              <b-button size="sm" variant="flat-success" @click.prevent>Edit</b-button> |
              <b-button size="sm" variant="flat-danger" @click.prevent>Delete</b-button>
            </b-col>
          </b-row>
        </template>
        <hr />
        <span>* Indicates that this commodity may be subject to additional duty</span>
        <b-button v-if="!ticket.tariffcodeemailsent" size="sm" variant="success" class="float-right">HTS Verification Complete</b-button>
        <span v-else>
          Confirmation email sent to user on {{ ticket.tariffcodeemailsent | dateFormat('MM/DD/YYYY') }} @
          {{ ticket.tariffcodeemailsent | dateFormat('hh:mm a') }}
        </span>
      </div>
      <div v-else>
        <span>No tariff codes have yet been associated with this ticket.</span>
      </div>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle, BButton, BCollapse, VBToggle, BRow, BCol } from 'bootstrap-vue'

import { dateFormat } from '@/utils/filters'

export default {
  components: {
    BCard,
    BCardTitle,
    BButton,
    BCollapse,
    BRow,
    BCol,
  },
  directives: {
    'b-toggle': VBToggle,
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
    ticket: {
      type: Object,
      required: true,
    },
    htsCodes: {
      type: Array,
      required: true,
    },
  },

  computed: {
    community() {
      const { communities, commodity_uhids } = (this.getFullTicket.hts_code_assignment || []).reduce(
        (accum, i) => {
          if (i.tuhid) {
            accum.communities.push(i)
            accum.commodity_uhids.push(i.uhid)
          }
          return accum
        },
        { communities: [], commodity_uhids: [] },
      )
      let className = ''
      if (!communities.length) {
        className = 'ticket-htsr-collapse-card-danger'
      } else if (this.getFullTicket.tariffCodeEmailSent) {
        className = 'ticket-htsr-collapse-card-success'
      } else {
        className = 'ticket-htsr-collapse-card-warning'
      }
      return {
        communities,
        commodity_uhids,
        className,
      }
    },
    htsCodeAssignments() {
      return this.getFullTicket.hts_code_assignment || []
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/base/bootstrap-extended/include'; // Bootstrap includes

#ticket-htsr-collapse-card {
  &.ticket-htsr-collapse-card-danger {
    background-color: rgba($danger, 0.3);
  }
  &.ticket-htsr-collapse-card-warning {
    background-color: rgba($yellow, 0.3);
  }
  &.ticket-htsr-collapse-card-success {
    background-color: rgba($success, 0.3);
  }
  .collapse {
    background-color: white;
    color: black;
  }
  .border-x {
    margin-top: 0.5rem;
    padding: 0.25rem 0rem 0.25rem 0rem;
    border-top: 1px solid rgba($dark, 0.5);
    border-bottom: 1px solid rgba($dark, 0.5);
  }
}
</style>
