FROM mysql:8.0-debian
USER root

RUN apt-get update && \
    apt-get install -y procps iproute2 && \
    rm -rf /var/lib/apt/lists/*
