<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const showResults = ref(false);
const calculationResults = ref(null);

const form = useForm({
    loan_amount: "",
    annual_interest_rate: "",
    start_date: "",
    end_date: "",
    repayment_type: "repayment",
    name: "",
});

const calculateLoan = () => {
    const formData = {
        ...form.data(),
        annual_interest_rate: parseFloat(form.annual_interest_rate) / 100,
        loan_amount: parseInt(form.loan_amount),
    };

    axios
        .post("/api/loan/calculate", formData)
        .then((response) => {
            calculationResults.value = response.data;
            showResults.value = true;
        })
        .catch((error) => {
            if (error.response?.data?.errors) {
                Object.keys(error.response.data.errors).forEach((key) => {
                    form.setError(key, error.response.data.errors[key][0]);
                });
            } else {
                console.error("Calculation error:", error);
            }
        });
};

const resetForm = () => {
    form.reset();
    showResults.value = false;
    calculationResults.value = null;
    form.clearErrors();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-GB", {
        style: "currency",
        currency: "GBP",
    }).format(amount);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-GB", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <div>
        <div>
            <form @submit.prevent="calculateLoan" class="space-y-6 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="loan_amount" value="Loan Amount (£)" />
                        <TextInput
                            id="loan_amount"
                            v-model="form.loan_amount"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="100000"
                            min="1"
                            step="1"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.loan_amount"
                        />
                        <p class="mt-1 text-sm">
                            Enter the amount you want to borrow in pounds
                        </p>
                    </div>

                    <div>
                        <InputLabel
                            for="annual_interest_rate"
                            value="Annual Interest Rate (%)"
                        />
                        <TextInput
                            id="annual_interest_rate"
                            v-model="form.annual_interest_rate"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="9.0"
                            min="0"
                            max="100"
                            step="0.1"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.annual_interest_rate"
                        />
                        <p class="mt-1 text-sm">
                            Enter the yearly interest rate as a percentage
                        </p>
                    </div>

                    <div>
                        <InputLabel for="start_date" value="Start Date" />
                        <TextInput
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.start_date"
                        />
                        <p class="mt-1 text-sm">
                            When the money lands in your bank
                        </p>
                    </div>

                    <div>
                        <InputLabel for="end_date" value="End Date" />
                        <TextInput
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.end_date"
                        />
                        <p class="mt-1 text-sm">
                            When the loan is meant to finish
                        </p>
                    </div>

                    <div>
                        <InputLabel
                            for="repayment_type"
                            value="Repayment Type"
                        />
                        <select
                            id="repayment_type"
                            v-model="form.repayment_type"
                            class="select w-full block mt-1"
                            required
                        >
                            <option value="repayment">Repayment</option>
                            <option value="interest-only">Interest Only</option>
                            <option value="interest-retained">
                                Interest Retained
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.repayment_type"
                        />
                        <p class="mt-1 text-sm">
                            Select your preferred repayment method
                        </p>
                    </div>

                    <div>
                        <InputLabel for="name" value="Borrower Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="John Smith"
                            maxlength="255"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                        <p class="mt-1 text-sm">
                            Enter the name of the borrower
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <SecondaryButton
                        type="button"
                        @click="resetForm"
                        :disabled="form.processing"
                    >
                        Reset
                    </SecondaryButton>

                    <PrimaryButton
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Calculating...</span>
                        <span v-else>Calculate Loan</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <div v-if="showResults && calculationResults">
            <h2 class="text-xl font-semibold mb-4">
                Loan Calculation Results for
                {{ calculationResults.borrower_name }}
            </h2>

            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
            >
                <div class="bg-base-200 p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium mb-2">
                        Average Monthly Payment
                    </h3>
                    <p class="text-primary text-2xl font-bold">
                        {{
                            formatCurrency(
                                calculationResults.summary.monthly_payment
                            )
                        }}
                    </p>
                </div>

                <div class="bg-base-200 p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium mb-2">Total Interest</h3>
                    <p class="text-2xl font-bold text-secondary">
                        {{
                            formatCurrency(
                                calculationResults.summary.total_interest
                            )
                        }}
                    </p>
                </div>

                <div class="bg-base-200 p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium mb-2">Total Amount Paid</h3>
                    <p class="text-2xl font-bold text-accent">
                        {{
                            formatCurrency(
                                calculationResults.summary.total_paid
                            )
                        }}
                    </p>
                </div>

                <div class="bg-base-200 p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium mb-2">Loan Duration</h3>
                    <p class="text-2xl font-bold text-warning">
                        {{ calculationResults.summary.total_days }}
                        days
                    </p>
                </div>
            </div>

            <div class="bg-base-200 p-4 rounded-lg shadow-sm">
                <h3 class="text-lg font-medium">Loan Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium"
                            >Daily Principal Payment:</span
                        >
                        <span class="ml-2 text-gray-900 dark:text-white">
                            {{
                                formatCurrency(
                                    calculationResults.summary
                                        .daily_principal_payment
                                )
                            }}
                        </span>
                    </div>
                    <div>
                        <span class="font-medium">Final Payment Date:</span>
                        <span class="ml-2 text-gray-900 dark:text-white">
                            {{
                                formatDate(
                                    calculationResults.summary.final_payment
                                )
                            }}
                        </span>
                    </div>
                </div>
            </div>
            <!--

            <div
                v-if="
                    calculationResults.summary.daily_payments &&
                    calculationResults.summary.daily_payments.length > 0
                "
                class="mt-6 bg-base-200 p-4 rounded-lg shadow-sm"
            >
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Payment Schedule Preview
                </h3>
                <div
                    class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100 max-h-90"
                >
                    <table class="table table-xs table-pin-rows">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Date</th>
                                <th>Interest</th>
                                <th>Principal</th>
                                <th>Total Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="payment in calculationResults.summary
                                    .daily_payments"
                                :key="payment.day"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td
                                    class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                >
                                    {{ payment.day }}
                                </td>
                                <td
                                    class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                >
                                    {{ formatDate(payment.date) }}
                                </td>
                                <td
                                    class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(payment.daily_interest) }}
                                </td>
                                <td
                                    class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            payment.principal_payment
                                        )
                                    }}
                                </td>
                                <td
                                    class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(payment.total_payment) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->
        </div>
    </div>
</template>
