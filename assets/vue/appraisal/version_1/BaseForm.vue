<template>
    <form id="appraisal" method="POST" action="#" @input="onInput">
        <div class="form-group">
            <h5 class="title">
                Employee Information
            </h5>
        </div>
        <div class="btn" @click="dump">Dump</div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="staff-name">Staff Name</label>
                    <input type="text" class="form-control" id="staff-name" name="staff_name" v-model="form.staffName"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" class="form-control" v-model="form.department"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Position</label>
                    <input type="text" class="form-control" v-model="form.position"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Office</label>
                    <input type="text" class="form-control" v-model="form.office"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Appraising Officer</label>
                    <input type="text" class="form-control" v-model="form.ao"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Countersigning Officer</label>
                    <input type="text" class="form-control" v-model="form.coJoin"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Period of Evaluation</label>
                    <input type="text" class="form-control" v-model="form.period"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Commencement Date</label>
                    <input type="text" class="form-control" v-model="form.commenceDate"/>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label>Type of Appraisal</label>
                    <input type="text" class="form-control" v-model="form.appType"/>
                </div>
            </div>
        </div>
        <div class="row">
            <small class="col mb-3">
                Scoring Scheme: Below Deputy General Manager Level - Part A (50%) + Part B (50%); Deputy General Manager Level and above - Part A (50%) + Part B1 (30%) + Part B2 (20%)
            </small>
        </div>
        <div class="row">
            <h5 class="col title">
                Part A: Accomplishments of Key Responsibilities or Objectives
            </h5>
        </div>
        <div class="row">
            <small class="col mb-2">
                For Employee: please state at least 3 key responsibilities or objectives under the direction of your supervisor, and conduct a self-evaluation on your achievements/results achieved.
            </small>
        </div>
        <div class="row">
            <small class="col mb-2">
                For Appraising Officer: please evaluate the employee's key responsibilities and results achieved, then assign a reasonable score.
            </small>
        </div>
        <div class="row">
            <small class="col mb-2">
                * Weight: Appraising Officer has to judge and give weight of each key responsibility that their subordinates are responsible for in terms of importance. Total weight must be equal to 100%.
            </small>
        </div>

        <div class="row mt-3">
            <div class="offset-1 col-6">
                <h6>
                    For Employee
                </h6>
            </div>
            <div class="col">
                <h6>
                    For Appraising Office
                </h6>
            </div>

        </div>
        <div id="part-a-wrapper">
            <ListItemA v-for="(row, index) in form.partA" :key="index"
               v-bind:index="index" v-bind:row="row"
               v-on:delete-a="DeleteA"
            />
            <div class="row mt-3 md-3">
                <div class="col text-center ">
                    <i class="material-icons text-primary p-2 btn-like" v-on:click="AddA">add</i>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col title">
                Part B: Competencies Assessment
            </div>
        </div>
        <div class="row">
            <p class="col">
                This part should be completed by both the Appraising Officer and the Employee.
            </p>
        </div>
        <div class="row">
            <div class="col title">
                Part B1: This section is applicable to all employees
            </div>
        </div>
        <div id="part-b1-wrapper" class="row">
            <div class="col">
                <div class="row">
                    <div class="col">
                        <p>Teamwork and Support</p>
                        <div class="row">
                            <div class="col">
                                <p>5. Fosters team spirit, encourages others to contribute and draws on wide variety of others' skills to achieve team success.</p>
                                <p>4. Cooperates with colleagues, willingly shares team values, listens, makes a constructive contribution to teams and builds on team success.</p>
                                <p>3. Liaises with colleagues, willingly shares team information and knowledge and makes a constructive contribution to teams. Recognize one's limit and seek for support without delay.</p>
                                <p>2. Did not demonstrate the willingness to work amicably with colleagues or proactively support others in times of need.</p>
                                <p>1. Behaves in a disruptive manner within team, is confrontational and negatively criticises others and their contributions. Not considered a team worker.</p>
                            </div>
                            <div class="col">
                                Self Assessment
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    import ListItemA from "./ListItemA";
    import axios from "axios";

    export default {
        name: "App",
        components: {ListItemA},
        props: [
            'apiPath'
        ],
        data: function() {
            return {
                form: {
                    staffName: "",
                    department: "",
                    position: "",
                    office: "",
                    ao: "",
                    coJoin: "",
                    co1: "",
                    co2: "",
                    period: "",
                    commenceDate: "",
                    appType: "",
                    partA: []
                }
            }
        },
        mounted: function () {
            axios.get(this.apiPath).then((ajax) => {
                let appData = ajax.data;
                this.form.staffName = appData["staff_name"];
                this.form.department = appData["staff_department"];
                this.form.position = appData["staff_position"];
                this.form.office = appData["staff_office"];
                this.form.ao = appData["appraiser_name"];
                this.form.coJoin = appData["countersigner_name"];
                this.form.co1 = appData["countersigner_1_name"];
                this.form.co2 = appData["countersigner_2_name"];
                this.form.period = appData["survey_period"];
                this.form.commenceDate = appData["survey_commencement_date"];
                this.form.appType = "Annual Appraisal";
                this.form.partA = [];
                for (var k in appData["part_a"]) {
                    this.form.partA.push(appData["part_a"][k]);
                }

            });
        },
        methods: {
            AddA: function(event) {
                this.form.partA.push({});
            },
            DeleteA: function(event) {
                this.form.partA.splice(event.index, 1)
            },
            dump: function(event) {
                console.log(this.form)
            },
            onInput: function (event) {
                console.log(event)
            }
        }
    }
</script>

<style scoped>

</style>