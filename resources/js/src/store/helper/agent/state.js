const defaultAgent = { displayInternally: true, isActive: false, displayinternally: true, permissions: [] }
const defaultAgentStatus = {
  statusName: 'New Status',
}

export default {
  defaultAgent,
  agent: { ...defaultAgent },
  loading: false,
  allAgents: [],
  allPermissions: [],
  allInternalAgents: [],
  defaultAgentStatus,
  agentStatus: { ...defaultAgentStatus },
  agentStatusList: [],
}
