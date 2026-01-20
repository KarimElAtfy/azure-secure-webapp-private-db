# Deployment Guide

This section describes how to deploy the entire architecture from scratch using the Azure Portal.  
All resources are deployed within a single resource group for clarity and simplicity.

---

### Prerequisites

Before deploying this project, ensure you have:

- An active Azure subscription
- Contributor access to an Azure Resource Group
- Basic familiarity with the Azure Portal
- A modern browser with SSH enabled (for Azure Bastion)

---

### 1. Resource Group

Create a new resource group to host all project resources.

- **Resource Group name:** `az-104-course`
- **Region:** West Europe

All subsequent resources are deployed into this resource group.

---

### 2. Virtual Network and Subnets

Create an Azure Virtual Network to isolate private resources.

#### Virtual Network
- **Name:** `vnet-prod-104`
- **Address space:** `10.10.0.0/16`

#### Subnets

Create the following subnets:

| Subnet Name | Address Range | Purpose |
|------------|--------------|--------|
| `snet-webapp` | `10.10.1.0/24` | Web App VNet Integration |
| `snet-db` | `10.10.2.0/24` | Database virtual machine |
| `AzureBastionSubnet` | `10.10.10.0/27` | Azure Bastion (required name) |

#### Subnet Delegation

Delegate the `snet-webapp` subnet to:

- **Service:** `Microsoft.Web/serverFarms`

This delegation is required for Azure Web App VNet Integration.

---

### 3. Network Security Group (Database)

Create a Network Security Group to restrict database access.

#### NSG
- **Name:** `nsg-db`
- **Region:** West Europe

Associate the NSG with the `snet-db` subnet.

#### Inbound Rule

Allow MySQL traffic **only** from the Web App subnet:

- **Source:** IP Addresses  
- **Source address:** `10.10.1.0/24`  
- **Destination port:** `3306`  
- **Protocol:** TCP  
- **Action:** Allow  
- **Priority:** 100  

All other inbound traffic remains implicitly denied.

---

### 4. Database Virtual Machine

Deploy a Linux virtual machine to host MySQL.

#### VM Configuration
- **Name:** `vm-db-104`
- **Image:** Ubuntu Server 22.04 LTS
- **Size:** Standard_B1s
- **Authentication:** Username + Password or SSH key
- **Public IP:** None

#### Networking
- **Virtual network:** `vnet-prod-104`
- **Subnet:** `snet-db`
- **NIC NSG:** None (security enforced at subnet level)

#### MySQL Installation

Install and configure MySQL:

- Install `mysql-server`
- Enable remote connections (`bind-address = 0.0.0.0`)
- Create:
  - Database: `appdb`
  - User: `appuser`
- Grant privileges on `appdb`

---

### 5. Azure Bastion

Deploy Azure Bastion to enable secure administrative access.

#### Bastion Configuration
- **Name:** `bastion-104`
- **Virtual network:** `vnet-prod-104`
- **Subnet:** `AzureBastionSubnet`
- **Public IP:** Standard SKU

Use Bastion to connect to the database VM via SSH without exposing port 22 publicly.

---

### 6. Azure Private DNS

Create a private DNS zone for internal name resolution.

#### Private DNS Zone
- **Zone name:** `internal.cloud`

#### Virtual Network Link
Link the DNS zone to:
- **Virtual network:** `vnet-prod-104`
- **Auto-registration:** Disabled

#### DNS Record
Create an A record:
- **Name:** `db`
- **IP address:** Private IP of `vm-db-104`

This enables resolution of `db.internal.cloud` inside the VNet.

---

### 7. Azure Web App

Deploy the application frontend using Azure App Service.

#### App Service Plan
- **Name:** `asp-web-104`
- **SKU:** Basic B1
- **OS:** Linux

#### Web App
- **Name:** `app-web-104-<unique>`
- **Runtime:** PHP 8.x
- **Region:** West Europe

Verify that the default App Service page loads successfully.

---

### 8. Virtual Network Integration

Enable outbound private connectivity for the Web App.

- **Virtual network:** `vnet-prod-104`
- **Subnet:** `snet-webapp`
- **Route all traffic:** Enabled

After integration, the Web App can access private resources inside the VNet.

---

### 9. Application Configuration

Configure database connection settings using App Service Application Settings.

Add the following application settings:

| Name | Value |
|----|------|
| `DB_HOST` | `db.internal.cloud` |
| `DB_NAME` | `appdb` |
| `DB_USER` | `appuser` |
| `DB_PASSWORD` | *(database password)* |

These settings are injected at runtime and are not stored in source code.

---

### 10. Application Deployment

Deploy the PHP application located in `webapp/index.php` to the Web App.

The application:
- Resolves the database hostname using Azure Private DNS
- Connects to MySQL over private IP
- Confirms end-to-end private connectivity

Expected result:
- Web page displays **“Connected to DB successfully”**

---

### Deployment Outcome

At the end of this deployment:

- The database has no public exposure
- The Web App accesses the database privately
- DNS resolution is handled internally
- Administrative access is secured using Azure Bastion

This architecture reflects a secure, production-style Azure design.

---

### Notes

This deployment intentionally avoids:

- Public database endpoints
- Hard-coded credentials
- Inbound management ports from the internet

The focus is on security, clarity, and reproducibility.
