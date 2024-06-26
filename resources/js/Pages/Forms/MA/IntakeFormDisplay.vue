<template>
  <div class="flex flex-col">
    <div class="container mx-auto">
      <div class="w-full">
        <form
          @submit.prevent="submit"
          class="bg-white shadow-md rounded px-12 pt-12 pb-8 mb-4 mt-20"
        >
          <label class="block mt-4">
            <span class="text-gray-700 mt-2">
              I acknowledge that Modern Acupuncture seeks to provide the highest
              level of acupuncture care by only employing acupuncturists who are
              licensed and have graduated from accredited Master’s level
              programs consisting of at least 2,000 clock hours of combined
              instruction and clinical practice. Additionally they are required
              to pass rigorous board exams and meet state specific licensing
              requirements.
            </span>
          </label>
          <div class="mt-4">
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio h-6 w-6 text-gray-500"
                  name="acknowledgement"
                  disabled
                  v-model="form.acknowledgement"
                  value="yes"
                />
                <span class="ml-2 font-bold">YES I ACKNOWLEDGE</span>
              </label>
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              PATIENT INFORMATION
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Name</span>
              <input
                type="text"
                class="form-input mt-1 block w-full mb-2 bg-gray-200"
                v-model="form.first_name"
                disabled
              />
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.last_name"
                disabled
              />
            </div>
            <div class="w-full md:w-1/2 mb-2 px-3 md:mb-0">
              <label class="block">
                <span class="text-gray-700 font-bold">Gender</span>
                <input
                  type="text"
                  class="form-input mt-1 block w-full bg-gray-200"
                  v-model="form.gender"
                  disabled
                />
              </label>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Date Of Birth</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.dob"
                disabled
              />
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Age</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.age"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Email Address</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.email"
                disabled
              />
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Mobile</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.mobile"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Address</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.address"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full bg-gray-200"
                v-model="form.city"
                disabled
              />
            </div>
            <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full bg-gray-200"
                v-model="form.state"
                disabled
              />
            </div>
            <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full bg-gray-200"
                v-model="form.postal_code"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Patient Occupation</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.patient_occupation"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Emergency Contact Name</span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.emergency_contact_name"
                disabled
              />
            </div>
            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Emergency Contact Phone</span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.emergency_contact_phone"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had acupuncture treatments before?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="acupuncture_before"
                    value="yes"
                    disabled
                    v-model="form.acupuncture_before"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="acupuncture_before"
                    value="no"
                    disabled
                    v-model="form.acupuncture_before"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Initials</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                disabled
                v-model="form.initial_patient_information"
              />
            </div>

            <div class="w-full md:w-4/5 px-3 mt-5 mb-2 md:mb-0">
              <span class="text-gray-700"
                >I understand that occasionally, there could be minor bruising
                where the needles are inserted. To keep this to a minimum,
                Modern Acupuncture uses the highest quality needles available,
                which also provide the most comfortable treatment with virtually
                no pain.</span
              >
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              MEDICAL HISTORY
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Height: (format example: 5'10'')</span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.height"
                disabled
              />
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Weight: (LBS.)</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.weight"
                disabled
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently taking blood thinners or on an aspirin
                regimen?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.blood_thinner_medicine"
                    disabled
                    name="blood_thinner_medicine"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.blood_thinner_medicine"
                    disabled
                    name="blood_thinner_medicine"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently pregnant or trying to conceive?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.pregnancy"
                    name="pregnancy"
                    disabled
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.pregnancy"
                    name="pregnancy"
                    disabled
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a seizure disorder?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.seizure_disorder"
                    disabled
                    name="seizure_disorder"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.seizure_disorder"
                    disabled
                    name="seizure_disorder"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Do you have HIV/AIDs?</span>
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.hiv"
                    name="hiv"
                    value="yes"
                    disabled
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.hiv"
                    name="hiv"
                    value="no"
                    disabled
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have Hepatitis B?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.hepatitis_b"
                    name="hepatitis_b"
                    disabled
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.hepatitis_b"
                    name="hepatitis_b"
                    disabled
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a known nickel allergy?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.nickel_allergy"
                    disabled
                    name="nickel_allergy"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    v-model="form.nickel_allergy"
                    disabled
                    name="nickel_allergy"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="block">
            <span class="text-gray-700 font-bold"
              >What are your concerns and reasons for visiting today? (select
              all that apply)</span
            >
            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        name="today_digestive_issue"
                        v-model="form.today_digestive_issue"
                        disabled
                      />
                      <span class="ml-2">Digestive issues</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_stress"
                        disabled
                        name="today_stress"
                      />
                      <span class="ml-2">Stress</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_insomnia"
                        disabled
                        name="today_insomnia"
                      />
                      <span class="ml-2">Insomnia</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_anxiety"
                        disabled
                        name="today_anxiety"
                      />
                      <span class="ml-2">Anxiety</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_high_blood_pressure"
                        disabled
                        name="today_high_blood_pressure"
                      />
                      <span class="ml-2">High Blood Pressure</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_depression"
                        disabled
                        name="today_depression"
                      />
                      <span class="ml-2">Depression</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_allergies"
                        disabled
                        name="today_allergies"
                      />
                      <span class="ml-2">Allergies</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_back_pain"
                        disabled
                        name="today_back_pain"
                      />
                      <span class="ml-2">Back Pain</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_muscle_sprain"
                        disabled
                        name="today_muscle_sprain"
                      />
                      <span class="ml-2">Muscle Sprain or Strain</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_headaches"
                        disabled
                        name="today_headaches"
                      />
                      <span class="ml-2">Headaches</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_fatigue"
                        disabled
                        name="today_fatigue"
                      />
                      <span class="ml-2">Fatigue</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_joint_pain"
                        disabled
                        name="today_joint_pain"
                      />
                      <span class="ml-2">Joint pain</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_menstrual_cramps"
                        disabled
                        name="today_menstrual_cramps"
                      />
                      <span class="ml-2">Menstrual Cramps/PMS</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-2/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.today_muscle_aches"
                        disabled
                        name="today_muscle_aches"
                      />
                      <span class="ml-2">Muscle Aches/PMS</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Of the conditions you selected, which is the most
                prominent?</span
              >
              <input
                type="text"
                name="most_prominent_reason"
                class="form-input mt-1 block w-full bg-gray-200"
                disabled
                v-model="form.most_prominent_reason"
              />
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >How long have you been experiencing symptoms?</span
              >
              <input
                type="text"
                name="current_symptoms_duration"
                class="form-input mt-1 block w-full bg-gray-200"
                disabled
                v-model="form.current_symptoms_duration"
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently under a physicians care for this
                condition?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="under_physician_care"
                    disabled
                    v-model="form.under_physician_care"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="under_physician_care"
                    disabled
                    v-model="form.under_physician_care"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >If yes, what is your diagnosis?</span
              >
              <input
                type="text"
                name="under_physician_care_diagnosis"
                disabled
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.under_physician_care_diagnosis"
              />
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              COSMETIC ACUPUNCTURE
            </div>
          </div>

          <label class="block mt-4 mb-2">
            <span class="text-gray-700 mt-2"
              >If you are here for Cosmetic Acupuncture, please complete the
              following questions:</span
            >
          </label>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you received Botox or dermal filler injections in the last
                2 weeks?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="botox_dermal_filler"
                    disabled
                    v-model="form.botox_dermal_filler"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="botox_dermal_filler"
                    disabled
                    v-model="form.botox_dermal_filler"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had any cosmetic surgical procedures within the last 6
                weeks?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="cosmetic_surgical_procedures"
                    disabled
                    v-model="form.cosmetic_surgical_procedures"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="cosmetic_surgical_procedures"
                    disabled
                    v-model="form.cosmetic_surgical_procedures"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a tendency to bruise easily?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="tendency_to_bruise"
                    disabled
                    v-model="form.tendency_to_bruise"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="tendency_to_bruise"
                    disabled
                    v-model="form.tendency_to_bruise"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you get migraine headaches once per month or more?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="migraine"
                    v-model="form.migraine"
                    disabled
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="migraine"
                    v-model="form.migraine"
                    disabled
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had a recent head injury or concussion?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="head_injury"
                    v-model="form.head_injury"
                    disabled
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6 text-gray-500"
                    name="head_injury"
                    v-model="form.head_injury"
                    disabled
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="block">
            <span class="text-gray-700"
              >Cosmetic visits include targeted areas of treatment on the face.
              Please select up to two areas you would like to focus on:</span
            >
            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_smile_lines"
                        disabled
                        name="cosmetic_smile_lines"
                      />
                      <span class="ml-2">Smile lines</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_crows_feet"
                        disabled
                        name="cosmetic_crows_feet"
                      />
                      <span class="ml-2">Crows Feet</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_forehead_lines"
                        disabled
                        name="cosmetic_forehead_lines"
                      />
                      <span class="ml-2">Forehead lines</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_surrounding_lips"
                        disabled
                        name="cosmetic_surrounding_lips"
                      />
                      <span class="ml-2">Lines surrounding lips</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_lines_between_eyebrows"
                        disabled
                        name="cosmetic_lines_between_eyebrows"
                      />
                      <span class="ml-2">Lines between eyebrows</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-2/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox text-gray-500 h-6 w-6"
                        v-model="form.cosmetic_marionette_lines"
                        disabled
                        name="cosmetic_marionette_lines"
                      />
                      <span class="ml-2"
                        >Marionette Lines (Sides of chin to edges of
                        mouth)</span
                      >
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              NOTICE OF PRIVACY PRACTICES
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="font-bold mb-2">
                Click below to read this office’s Notice of Privacy Practices.
              </p>
              <p class="mb-2">
                <a
                  href="https://www.modernacupuncture.com/privacy"
                  target="_blank"
                  class="text-blue-600"
                  >HIPAA ACKNOWLEDGMENT
                </a>
                - (Link will take you to a new browser window. Close or return
                to this page when done reviewing.)
              </p>
              <p class="mb-2">
                By signing this I am acknowledging that I have had full
                opportunity to read and consider the contents of this office’s
                Privacy Practices, which can be found at modacu.com/privacy. I
                further acknowledge and understand that, by signing this Patient
                Intake Form that I am giving my consent for this office’s use
                and disclosure of my protected health information to carry out
                treatment, payment activities and health care options, as
                outlined in the Notice of Privacy Practices disclosure in the
                link above.
              </p>
            </div>
          </div>
          <div class="mt-4 mb-2">
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio h-6 w-6 text-gray-500 h-6 w-6"
                  name="privacy_acknowledge"
                  disabled
                  v-model="form.privacy_acknowledge"
                  value="yes"
                />
                <span class="ml-2 font-bold">YES I ACKNOWLEDGE</span>
              </label>
            </div>
          </div>
          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-600"
                >If this acknowledgment is signed by a guardian (for a minor) or
                a personal representative on behalf of the patient please
                complete the following</span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                disabled
                v-model="form.initial_privacy_practices"
              />
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              INFORMED CONSENT FOR ACUPUNCTURE
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="mb-2">
                This Informed Consent for Acupuncture applies to the essential
                and cosmetic acupuncture. This consent will allow us to provide
                acupuncture treatments to you (or your dependents) now and in
                the future.
              </p>
              <p class="mb-2">
                <span class="font-bold">INTRODUCTION - </span>An acupuncture
                treatment involves the insertion of acupuncture needles at
                specific points on the body to boost the body’s natural
                painkillers, increase blood flow and trigger a healing response.
              </p>
              <p class="mb-2">
                An acupuncture facial treatment involves the insertion of
                acupuncture needles into fine lines and wrinkles on the face and
                neck in order to reduce the visible signs of aging. A facial
                acupuncture treatment addresses the entire body
                constitutionally, and is not merely “cosmetic.” An acupuncture
                facial involves the patient in an organic, gradual process, that
                is customized for each individual. It is no way analogous to, or
                a substitute for, a surgical “face lift”.
              </p>
              <p class="mb-2">
                <span class="font-bold">BENEFITS - </span>Facial acupuncture can
                increase facial tone, decrease puffiness around the eyes, as
                well as bring more firmness to sagging skin, enhance the
                radiance of the complexion, and flesh out sunken areas.
                Customarily, fine wrinkles will disappear, and deeper ones will
                be reduced. As this treatment is not merely confined to the
                face, but incorporates the entire body and constitutional issues
                of health.
              </p>
              <p class="mb-2">
                <span class="mb-2">ADDITIONAL CARE NECESSARY - </span
                >Acupuncture should be relaxing and patients will often feel
                very calm after a treatment. He/she may feel a little tired or
                sleepy and one should take this into account if he/she is
                planning to drive or use heavy machinery straight after your
                treatment. Patients should refrain from vigorous exercise after
                treatment and, ideally, give themselves a little time to rest.
                It is also advisable not to drink alcohol for several hours
                after treatment.
              </p>
              <p class="mb-2">
                <span class="font-bold">RISKS OF ACUPUNCTURE - </span>Methods of
                treatment within the Modern Acupuncture clinic are limited to
                acupuncture. Acupuncture is a generally safe method of
                treatment, but that it may have some side effects, including
                bruising, numbness or tingling near the needling sites that may
                last a few days, and dizziness or fainting. Unusual risks of
                acupuncture include spontaneous miscarriage, nerve damage and
                organ puncture, including lung puncture (pneumothorax).
                Infection is another possible risk, although the clinic uses
                sterile disposable needles and maintains a clean and safe
                environment. While this document describes the major risks of
                treatment, other side effects and risks may occur.
              </p>
              <p class="mb-2">
                Acupuncture, generally, has very few side effects and any that
                do occur are usually mild and self-correcting. Possible side
                effects and complications may include the following: SORENESS:
                After acupuncture, one might have soreness, minor bleeding or
                bruising at the needle sites. BLEEDING: It is possible, though
                very unusual, that you may have problems with bleeding during an
                acupuncture treatment. Should post-acupuncture bleeding occur,
                it will usually only consist of a few drops. Accumulations of
                blood under the skin may cause a bruise, or hematoma, which will
                resolve itself. INFECTION: Infection is very unusual after an
                acupuncture treatment. Should an infection occur, additional
                treatment, including antibiotics, may be necessary. NEEDLE
                SHOCK: Needle shock is a rare complication after an acupuncture
                treatment. Needle shock is a syndrome which occurs in a about 5%
                of acupuncture patients. It presents as general malaise, cold
                perspiration, nausea, and in extreme situations, loss of
                consciousness. ALLERGIC REACTIONS: In rare cases, an allergic
                reaction to the metal needle may require additional treatment.
                DELAYED HEALING: Delayed wound healing or wound disruption are a
                rare complication experienced by patients in the aftermath of an
                acupuncture treatment. There is a greater risk for smokers, who
                frequently have dry, sagging skin, which does not heal as
                readily as that of non-smokers.
              </p>
              <p class="mb-2">
                There are many variable conditions in addition to risk and
                potential complications that may influence the long term result
                from acupuncture facial treatments. Even though risks and
                complications occur infrequently, the risks cited are the ones
                that are particularly associated with an acupuncture facial
                treatment. Other complications and risks can occur but are even
                more uncommon. Should complications occur, other treatments may
                be necessary. The practice of acupuncture is not an exact
                science. Although good results are expected, there is no
                guarantee or warranty, either expressed or implied, on the
                results that may be obtained. DAMAGE TO DEEPER STRUCTURES:
                Deeper structures such as blood vessels and muscles are rarely
                damaged during the course of a facial acupuncture treatment. If
                this does occur, the injury may be temporary or permanent.
                ASYMMETRY: The human face is normally asymmetrical. Thus, there
                can be a variation from one side to the other in the results
                attained from a facial acupuncture treatment. BRUISING AND
                PUFFINESS: There is a possibility of bruising (hematomas),
                puffiness, blood, tingling, itching, warmth, pain or other
                symptoms at the site of the needle. NERVE INJURY: Injuries to
                the motor or sensory nerves rarely result from facial
                acupuncture treatments. Nerve injuries may cause temporary or
                permanent loss of facial movements and feeling. Such injuries
                may improve over time. Injury to sensory nerves of the face,
                neck and ear regions may cause temporary or more rarely
                permanent numbness. Painful nerve scarring is very rare.
                UNSATISFACTORY RESULT: There is the possibility of a poor result
                from an acupuncture facial. A patient may, from time to time, be
                disappointed with the results. LONG TERM EFFECTS: Subsequent
                alterations in facial appearance may occur as the result of the
                normal process of aging, weight loss or gain, sun exposure, or
                other circumstances not related to an acupuncture facial. An
                acupuncture facial does not arrest the aging process or produce
                permanent tightening of the face and neck. Future facial
                acupuncture maintenance treatments, or other treatments, may be
                necessary to maintain the results of an acupuncture facial.
              </p>
              <p class="mb-2">
                <span class="font-bold"
                  >OPEN SETTING, COMMUNICATIONS & RECORDING DEVICES - </span
                >All services will be provided in an open room setting where
                multiple patients will be receiving care. Patients may overhear
                some of other patients’ protected medical information during the
                course of care. Additionally, to the extent permitted by law, we
                may use cameras or other recording devices in a clinic. A notice
                will be posted at the clinic informing patients’ of the use of
                such devices.
              </p>
              <p class="mb-2">
                <span class="font-bold">METHODS OF CONTACTING YOU - </span>We
                may use your address, phone number, e-mail and clinical records
                to contact you with notifications, text messages, birthday and
                holiday related messages, health related information, or other
                topics as appropriate, based on the contact information you
                provided above. If contacting you by phone, we may leave a
                message on your answering machine or voicemail. You may still
                receive our services if you do not provide a phone number or
                email address.
              </p>
              <p class="mb-2">
                <span class="font-bold">DISCLAIMER - </span>Informed-consent
                documents are used to communicate information about the proposed
                procedure along with disclosure of risks. The informed consent
                process attempts to define principles of risk disclosure that
                should generally meet the needs of most patients in most
                circumstances. However, informed consent documents should not be
                considered all-inclusive in defining other methods of care and
                risks encountered. Our acupuncturists may provide patients with
                additional or different information which is based upon all the
                facts in each particular case and the present state of knowledge
                within the field of acupuncture. Informed consent documents are
                not intended to define or serve as the standard of acupuncture.
                Standards of acupuncture are determined on the basis of all of
                the facts involved in an individual case and are subject to
                change as scientific knowledge and technology advance and as
                practice patterns evolve. It is important that you read the
                above information carefully and have all of your questions
                answered before signing the following consent.
              </p>
              <p class="mb-2">
                <span class="font-bold">CONSENT FOR ACUPUNCTURE TREATMENT</span>
              </p>
              <ol class="ml-8 list-decimal">
                <li class="mb-2">
                  I acknowledge that no guarantee has been given by anyone as to
                  the results that may be obtained.
                </li>
                <li class="mb-2">
                  IT HAS BEEN EXPLAINED TO ME IN A WAY THAT I UNDERSTAND:
                  <ul class="mt-2 ml-4 list-disc">
                    <li class="mb-1">
                      THE ABOVE TREATMENT OR EXPOSURE TO BE UNDERTAKEN
                    </li>
                    <li class="mb-1">
                      THERE ARE RISKS TO THE PROCEDURE OR TREATMENT PROPOSED
                    </li>
                  </ul>
                </li>
                <li class="mb-2">
                  I hereby request and consent to the performance of acupuncture
                  treatments and other procedures within the scope of the
                  practice of acupuncture on me (or on the patient named below,
                  for whom I am legally responsible) by the licensed
                  acupuncturists practicing in the Modern Acupuncture clinic,
                  including those who now or in the future work at any Modern
                  Acupuncture clinic
                </li>
                <li class="mb-2">
                  If applicable, I WILL NOTIFY A CLINICAL STAFF MEMBER WHO IS
                  CARING FOR ME IF I AM OR BECOME PREGNANT.
                </li>
                <li class="mb-2">
                  While I do not expect the clinical staff to be able to
                  anticipate and explain all possible risks and complications of
                  treatment, I wish to rely on the clinical staff to exercise
                  judgment during the course of treatment which the clinical
                  staff thinks at the time, based upon the facts then known, is
                  in my best interest.
                </li>
                <li>
                  I understand that results are not guaranteed. I further
                  understand the clinical and administrative staff may review my
                  patient records and lab reports, but all my records will be
                  kept confidential and will not be released without my written
                  consent.
                </li>
              </ol>
            </div>
          </div>

          <div class="flex flex-wrap mb-2 mt-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Initials</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.initial_informed_consent_for_acupuncture"
                disabled
                name="initial_informed_consent_for_acupuncture"
              />
            </div>

            <div class="w-full md:w-4/5 px-3 mt-4 mb-2 md:mb-0">
              <span class="text-gray-700 text-sm"
                >By voluntarily signing below, I show that I have read, or have
                had read to me, the above consent to treatment, have been told
                about the risks and benefits of acupuncture and have had an
                opportunity to ask questions. Additionally, I affirm that I
                understand and agree to all the terms and conditions mentioned
                above. I also affirm that all of my information listed in this
                form is accurate and correct to the best of my knowledge.</span
              >
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              ARBITRATION AGREEMENT
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="mb-2">
                By signing an arbitration agreement, a patient and a healthcare
                practitioner agree to use a private, confidential, and expedited
                arbitration, rather than a public, lengthy and costly courtroom
                trial, to settle any future malpractice claims. In arbitration,
                a neutral arbitrator (quite often a retired judge) decides the
                case. By agreeing to arbitrate, the parties preserve their right
                to present their claims fully; however, they choose a specific
                forum for dispute resolution: an arbitration hearingrather than
                a judge or jury trial.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 1: </span>Agreement to
                Arbitrate: It is understood that any dispute as to medical
                malpractice, including whether any medical services rendered
                under this contract were unnecessary or unauthorized or were
                improperly, negligently or incompetently rendered, will be
                determined by submission to arbitration as provided by state and
                federal law, and not by a lawsuit or resort to court process,
                except as state and federal law provides for judicial review of
                arbitration proceedings. Both parties to this contract, by
                entering into it, are giving up their constitutional right to
                have any such dispute decided in a court of law before a jury,
                and instead are accepting the use of arbitration. An acupuncture
                treatment involves the insertion of acupuncture needles at
                specific points on your body to boost your bodys natural
                painkillers, increase blood flow and trigger a healing response.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 2: </span>All Claims Must be
                Arbitrated: It is also understood that any dispute that does not
                relate to medical malpractice, including disputes as to whether
                or not a dispute is subject to arbitration, as to whether this
                agreement is unconscionable, and any procedural disputes, will
                also be determined by submission to binding arbitration. It is
                the intention of the parties that this agreement bind all
                parties as to all claims, including claims arising out of or
                relating to treatment or services provided by the health care
                provider, including any heirs or past, present or future
                spouse(s) of the patient in relation to all claims, including
                loss of consortium. This agreement is also intended to bind any
                children of the patient whether born or unborn at the time of
                the occurrence giving rise to any claim. This agreement is
                intended to bind the patient and the health care provider and/or
                other licensed health care providers, preceptors, or interns who
                now or in the future treat the patient while employed by,
                working or associated with or serving as a back-up for the
                health care provider, including those working at the health care
                provider’s clinic or office or any other clinic or office
                whether signatories to this form or not.
              </p>
              <p class="mb-2">
                All claims for monetary damages exceeding the jurisdictional
                limit of the small claims court against the health care
                provider, and/or the health care provider’s associates,
                association, corporation, partnership, employees, agents and
                estate, must be arbitrated including, without limitation, claims
                for loss of consortium, wrongful death, emotional distress,
                injunctive relief, or punitive damages. This agreement is
                intended to create an open book account unless and until
                revoked.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 3: </span>Procedures and
                Applicable Law: A demand for arbitration must be communicated in
                writing to all parties. Each party shall select an arbitrator
                (party arbitrator) within thirty days, and a third arbitrator
                (neutral arbitrator) shall be selected by the arbitrators
                appointed by the parties within thirty days thereafter. The
                neutral arbitrator shall then be the sole arbitrator and shall
                decide the arbitration. Each party to the arbitration shall pay
                such party’s pro rata share of the expenses and fees of the
                neutral arbitrator, together with other expenses of the
                arbitration incurred or approved by the neutral arbitrator, not
                including counsel fees, witness fees, or other expenses incurred
                by a party for such party’s own benefit.
              </p>
              <p class="mb-2">
                Either party shall have the absolute right to bifurcate the
                issues of liability and damage upon written request to the
                neutral arbitrator.
              </p>
              <p class="mb-2">
                The parties consent to the intervention and joinder in this
                arbitration of any person or entity that would otherwise be a
                proper additional party in a court action, and upon such
                intervention and joinder, any existing court action against such
                additional person or entity shall be stayed pending arbitration.
              </p>
              <p class="mb-2">
                The parties agree that provisions of state and federal law,
                where applicable, establishing the right to introduce evidence
                of any amount payable as a benefit to the patient to the maximum
                extent permitted by la, limiting the right to recover non-
                economic losses, and the right to have a judgment for future
                damages conformed to periodic payments, shall apply to disputes
                within this Arbitration Agreement. The parties further agree
                that the Commercial Arbitration Rules of the American
                Arbitration Association shall govern any arbitration conducted
                pursuant to this Arbitration Agreement.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 4: </span>General Provision: All
                claims based upon the same incident, transaction, or related
                circumstances shall be arbitrated in one proceeding. A claim
                shall be waived and forever barred if (1) on the date notice
                thereof is received, the claim, if asserted in a civil action,
                would be barred by the applicable legal statute of limitations,
                or (2) the claimant fails to pursue the arbitration claim in
                accordance with the procedures prescribed herein with reasonable
                diligence.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 5: </span>Revocation: This
                agreement may be revoked by written notice delivered to the
                health care provider within 30 days of signature and, if not
                revoked, will govern all professional services received by the
                patient and all other disputes between the parties.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 6: </span>Effective as of the
                date of first professional services. If any provision of this
                Arbitration Agreement is held invalid or unenforceable, the
                remaining provisions shall remain in full force and shall not be
                affected by the invalidity of any other provision. I understand
                that I have the right to receive a copy of this Arbitration
                Agreement. By my signature below, I acknowledge that I have
                received a copy. I understand that methods of treatment are
                limited to acupuncture. I have been informed that acupuncture is
                a generally safe method of treatment, but that it may have some
                side effects, including bruising, numbness or tingling near the
                needling sites that may last a few days, and dizziness or
                fainting. Unusual risks of acupuncture include spontaneous
                miscarriage, nerve damage and organ puncture, including lung
                puncture (pneumothorax). Infection is another possible risk,
                although the clinic uses sterile disposable needles and
                maintains a clean and safe environment. I understand that while
                this document describes the major risks of treatment, other side
                effects and risks may occur.
              </p>
            </div>
          </div>
          <div class="flex flex-wrap mb-2 mt-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Initials</span>
              <input
                type="text"
                class="form-input mt-1 block w-full bg-gray-200"
                v-model="form.initial_retroactive_effect"
                disabled
                name="initial_retroactive_effect"
              />
            </div>

            <div class="w-full md:w-4/5 px-3 mt-4 mb-2 md:mb-0">
              <span class="text-gray-700 text-sm"
                >Retroactive Effect: If patient intends this agreement to cover
                services rendered before the date it is signed (for example,
                emergency treatment), patient should initial here.</span
              >
            </div>
          </div>

          <img :src="signature" alt="" />
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import Layout from '../../Shared/Layout'

  export default {
    metaInfo: { title: 'Forms' },
    layout: (h, page) => h(Layout, [page]),
    props: ['form_model', 'signature'],
    data() {
      return {
        form: this.form_model,
      }
    },
  }
</script>

<style scoped>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #4bc5e8, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }
</style>
