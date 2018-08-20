<template>
    <div class="row">
        <div class="d-flex col-1 justify-content-center align-items-center">
            <i class="material-icons text-danger p-1 btn-like" @click="$emit('delete-a', {
                index: index
            })">clear</i>
        </div>
        <div class="col-3 d-flex flex-column">
            <label>
                <small>
                    Key Responsibilities or Objectives:
                </small>
            </label>
            <textarea class="form-control flex-grow-1" v-model="row.respon_name" v-on:input="$emit('input', $event.target.value)"></textarea>
        </div>
        <div class="col-3 d-flex flex-column">
            <label>
                <small>
                    Achievements/Results Achieved
                </small>
            </label>
            <textarea class="form-control flex-grow-1" v-model="row.respon_result" v-on:input="$emit('input', $event.target.value)"></textarea>
        </div>
        <div class="col-3 d-flex flex-column">
            <label>
                <small>
                    Comments by Appraising Officer
                </small>
            </label>
            <textarea class="form-control flex-grow-1" v-model="row.respon_comment" v-on:input="$emit('input', $event.target.value)"></textarea>
        </div>
        <div class="col-2">
            <div class="row mb-2">
                <div class="">
                    <label>
                        <small>
                            Weight (%)
                        </small>
                    </label>
                    <input type="number" class="form-control" v-model="row.respon_weight" v-on:input="$emit('input', $event.target.value)"/>
                </div>
            </div>
            <div class="row">
                <div class="">
                    <label>
                        <small>
                            Score
                        </small>
                    </label>
                    <input type="number" class="form-control"  v-model="row.respon_score"/>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "ListItemA",
        props:[
            "row",
            "index"
        ],
        mounted: function() {
        }
    }
</script>

<style scoped>

</style>