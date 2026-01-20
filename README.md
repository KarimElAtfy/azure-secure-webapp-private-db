# Secure Azure Web App with Private Database Access
This project demonstrates a secure Azure architecture where an Azure Web App accesses a MySQL database hosted on a private Linux virtual machine.

The database is not exposed to the public internet and can only be reached through Azure Virtual Network integration and Private DNS.

## Project Objectives

- Deploy an Azure Web App using PaaS services
- Integrate the Web App with an Azure Virtual Network
- Host a MySQL database on a private Linux virtual machine
- Eliminate public exposure of the database
- Use Azure Private DNS for internal name resolution
- Secure administrative access using Azure Bastion

## Technologies Used

- Azure App Service (Web App)
- Azure Virtual Network
- Azure Virtual Network Integration
- Azure Private DNS
- Azure Virtual Machines (Linux)
- MySQL
- Azure Bastion
- Network Security Groups (NSG)

## Project Status

Completed – functional end-to-end private connectivity between Web App and database.
