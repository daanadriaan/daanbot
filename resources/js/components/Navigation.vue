<template>
    <div>
        <router-link replace :to="{name: 'maker', params:{id: flow.id}}" v-for="flow in $root.flows">
            {{ flow.name }}
        </router-link>
    </div>
</template>
