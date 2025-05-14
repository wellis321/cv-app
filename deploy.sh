#!/bin/bash

# Colors for better output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Starting deployment process for CV App${NC}"

# Step 1: Install dependencies
echo -e "${GREEN}Installing dependencies...${NC}"
npm install

# Step 2: Run linting
echo -e "${GREEN}Running linting checks...${NC}"
npm run lint
if [ $? -ne 0 ]; then
  echo -e "${RED}Linting failed. Please fix issues before deploying.${NC}"
  exit 1
fi

# Step 3: Build the application
echo -e "${GREEN}Building the application...${NC}"
npm run build
if [ $? -ne 0 ]; then
  echo -e "${RED}Build failed. Please fix issues before deploying.${NC}"
  exit 1
fi

# Step 4: Deploy to Vercel
echo -e "${GREEN}Deploying to Vercel...${NC}"
echo -e "${YELLOW}This assumes you have Vercel CLI installed and are logged in.${NC}"
echo -e "${YELLOW}If not, please run: npm i -g vercel && vercel login${NC}"

# Check if --prod flag was passed
if [ "$1" == "--prod" ]; then
  echo -e "${YELLOW}Deploying to PRODUCTION${NC}"
  vercel --prod
else
  echo -e "${YELLOW}Deploying to preview environment${NC}"
  echo -e "${YELLOW}Use --prod flag to deploy to production${NC}"
  vercel
fi

echo -e "${GREEN}Deployment process completed!${NC}"