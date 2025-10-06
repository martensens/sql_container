
@description('Eine global eindeutiger Name für die ACR')
param acrName string = 'acr${uniqueString(resourceGroup().id)}' 

@description('Ein Standort für die ACR')
param location string = resourceGroup().location


resource acrRegistry 'Microsoft.ContainerRegistry/registries@2025-04-01' = {
  name: acrName
  location: location
  sku: {
    name: 'Basic'
  }
  properties:{
    adminUserEnabled: true
  }
}
