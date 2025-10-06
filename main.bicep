targetScope = 'subscription'

@description('Umgebung')
param environment string = 'dev'

@description('Name des Projektes')
param projectname string = 'infrastruktur'

@description('Ort der Ressourcengruppe')
param location string = 'westeurope'

var resourcegroupname = 'rg_${environment}_${projectname}'

resource rg 'Microsoft.Resources/resourceGroups@2025-04-01' = {
  name: resourcegroupname
  location: location
}

