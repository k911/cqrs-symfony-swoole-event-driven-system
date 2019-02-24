#!/usr/bin/env bash

### Start project
docker-compose build

# Console Tab 1
watch -- kubectl get pods

# Console Tab 2
kubectl apply -f kubernetes
kubectl get services

# Open browser windows:

# Present documentation - http://localhost:30080
# GraphQL request - http://localhost:30080/api/graphql
"""
query {
  userEvents {
    edges {
      node {
        createdAt
        eventData
        eventType
      }
    }
  }
}
"""
# Subscribe to server-sent events - http://localhost:30333
"""
http://localhost/api/reviews
http://localhost/api/users
"""

# Console Tab 2
kubectl get deployments

# Console Tab 3
kubectl logs deployment/review-system -f

# Console Tab 4
kubectl logs deployment/review-system-event-consumer -f

# Console Tab 5
kubectl logs deployment/review-system-command-consumer -f

# Increase Command Consumer
kubectl scale deployment --replicas 2 review-system-command-consumer
