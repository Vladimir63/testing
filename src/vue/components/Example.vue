<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="title main__title">{{ message }}</h1>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: null,
            }
        },
        created() {
            this.message = 'This is Vue component';
        },
    }
</script>